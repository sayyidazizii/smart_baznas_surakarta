<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\MustahikWorksheet;
use App\Models\MustahikWorksheetItem;
use App\Models\MustahikWorksheetResult;
use App\Models\MustahikWorksheetRequisition;
use App\Models\TransServiceRequisition;
use App\Models\TransServiceDisposition;
use App\Models\TransServiceGeneral;
use App\Models\TransServiceLog;
use App\Models\CoreSection;
use App\Models\CoreService;
use Illuminate\Support\Facades\Storage;
use DateTime;

class APIController extends Controller
{

    public function __construct()
    {
    }

    public function login(Request $request){
        $fields = $request->validate([
            'username'   => 'required|string',
            'password'   => 'required|string',
        ]);

        // Check username
        $user = User::select('system_user.*', 'system_user_group.user_group_name')
        ->join('system_user_group', 'system_user_group.user_group_id', 'system_user.user_group_id')
        ->where('name', $fields['username'])
        ->first();

        //Check password
        if(!Hash::check($fields['password'], $user->password)){
            return response([
                'message' => 'Username / Password Tidak Sesuai'
            ],401);
        }

        $token = $user->createToken('token-name')->plainTextToken;
        $response = [
            'data'  => $user,
            'token' => $token,
        ];

        return response($response, 201);
    }

    public function logout(Request $request){
        $user = auth()->user();
        $user_state = User::findOrFail($user['user_id']);
        $user_state->save();

        auth()->user()->tokens()->delete();

        return [
            'message' => 'Logged Out'
        ];
    }

    public function userProfile(Request $request){
        $fields = $request->validate([
            'user_id'   => 'required|string',
        ]);

        // Check username
        $user = User::select('system_user.name, system_user.created_at, system_user_group.user_group_name')
        ->join('system_user_group', 'system_user_group.user_group_id', 'system_user.user_gorup_id')
        ->where('system_user.user_id', $fields['user_id'])
        ->first();

        $response = [
            'data'  => $user,
        ];

        return response($response, 201);
    }

    public function changePassword(Request $request){
        $fields = $request->validate([
            'old_password'  => 'required|string',
            'new_password'  => 'required|string',
            'user_id'       => 'required|string',
        ]);

        // Check username
        $user = User::findOrFail($fields['user_id']);
        
        if(!Hash::check($fields['old_password'], $user->password)){ 
            return response([
                'message' => 'Password Lama Tidak Sesuai'
            ],401);
        }

        $user->password = Hash::make($fields['new_password']);
        if($user->save()){
            return response([
                'message' => 'Ganti Password Berhasil'
            ],201);
        }else{
            return response([
                'message' => 'Ganti Password Tidak Berhasil'
            ],401);
        }
    }
    
    public function getLoginState(Request $request){
        return response([
            'state'          => "login",
        ],201);
    }
    
    public function getWorksheetRequisition(Request $request){
        $fields = $request->validate([
            'user_id'       => 'required|string',
        ]);

        $worksheetrequisition = MustahikWorksheetRequisition::select('trans_service_disposition.service_requisition_no', 'trans_service_disposition.service_requisition_name', 'trans_service_disposition.service_requisition_phone', 'core_service.service_name', 'mustahik_worksheet_requisition.worksheet_requisition_id', 'mustahik_worksheet_requisition.service_requisition_id', 'mustahik_worksheet_requisition.service_id', 'mustahik_worksheet_requisition.user_id')
        ->join('trans_service_disposition', 'trans_service_disposition.service_requisition_id', 'mustahik_worksheet_requisition.service_requisition_id')
        ->join('core_service', 'core_service.service_id', 'trans_service_disposition.service_id')
        ->where('mustahik_worksheet_requisition.user_id', $fields['user_id'])
        ->where('trans_service_disposition.mustahik_status', 1)
        ->get();

        foreach($worksheetrequisition as $key => $val){
            $worksheet = MustahikWorksheet::select('worksheet_id', 'service_id', 'worksheet_no', 'worksheet_name', 'worksheet_type', 'worksheet_code')
            ->where('service_id', $val['service_id'])
            ->orderBy('worksheet_no', 'ASC')
            ->get();

            foreach($worksheet as $keyy => $vall){
                $worksheetitem = MustahikWorksheetItem::select('worksheet_item_id', 'worksheet_id', 'section_name', 'worksheet_item_name', 'worksheet_item_code')
                ->where('worksheet_id', $vall['worksheet_id'])
                ->get();
                
                $vall['worksheetitem'] = $worksheetitem;
            }

            $val['worksheet'] = $worksheet;
        }
        
        return response([
            'data' => $worksheetrequisition
        ],201);
    }

    public function insertWorksheetResult(Request $request){
        $fields = $request->validate([
            'user_id'                   => 'required|string',
            'worksheet_requisition_id'  => 'required|string',
            'service_requisition_id'    => 'required|string',
            'worksheet_result_data'     => 'required|string',
        ]);

        $resultdata = json_decode($fields['worksheet_result_data']);
        foreach($resultdata as $key => $val){
            $worksheet = MustahikWorksheet::select('worksheet_type')
            ->where('worksheet_code', $val->worksheet_code)
            ->first();

            if($worksheet['worksheet_type'] == 4){
                if($val->value != ''){
                    $fileNameToStore = $val->worksheet_code.'_'.time().".jpeg";
                    $image = base64_decode(preg_replace('#^data:image/[^;]+;base64,#', '', $val->value));
                    Storage::put('/public/worksheet-image/'.$fields['worksheet_requisition_id'] .'/'. $fileNameToStore, $image);
                    $val->value = $fileNameToStore;
                }
            }
        }

        $data = array (
            'worksheet_requisition_id'  => $fields['worksheet_requisition_id'],
            'user_id'                   => $fields['user_id'],
            'worksheet_result_data'     => json_encode($resultdata),
            'worksheet_result_date'     => date('Y-m-d'),
            'created_id'                => $fields['user_id'],
        );

        if(MustahikWorksheetResult::create($data)){
            $servicedisposition = TransServiceDisposition::where('service_requisition_id', $fields['service_requisition_id'])
            ->where('data_state', 0)
            ->first();
            $servicedisposition->mustahik_status = 2;
            $servicedisposition->save();

            return response([
                'message' => 'Simpan Data Berhasil'
            ],201);
        }else{
            return response([
                'message' => 'Simpan Data Tidak Berhasil'
            ],401);
        }
    }

    function usort_callback($a, $b){
        if ( $a['interval'] == $b['interval'] )
            return 0;

        return ( $a['interval'] > $b['interval'] ) ? -1 : 1;
    }

    public function getDashboard(Request $request){
        $fields = $request->validate([
            'start_date'    => 'required',
            'end_date'      => 'required',
        ]);

        $data               = array();
        $graphstuckarray    = array();
        $graphsectionarray  = array();
        $graphservicearray  = array();

        $transservicerequisition = TransServiceRequisition::select('trans_service_requisition.*')
        ->join('trans_service_disposition', 'trans_service_disposition.service_requisition_id', 'trans_service_requisition.service_requisition_id')
        ->where('trans_service_requisition.data_state', 0)
        ->where('trans_service_requisition.created_at', '>=', $fields['start_date'])
        ->where('trans_service_requisition.created_at', '<=', $fields['end_date']);
        if($request->section_id || $request->section_id != null || $request->section_id != ''){
            $transservicerequisition = $transservicerequisition->where('trans_service_disposition.section_id', $request->section_id);
        }
        if($request->service_id || $request->service_id != null || $request->service_id != ''){
            $transservicerequisition = $transservicerequisition->where('trans_service_requisition.service_id', $request->service_id);
        }
        if($request->kecamatan_id || $request->kecamatan_id != null || $request->kecamatan_id != ''){
            $transservicerequisition = $transservicerequisition->where('trans_service_disposition.kecamatan_id', $request->kecamatan_id);
        }
        $transservicerequisition = $transservicerequisition->get();

        foreach($transservicerequisition as $key => $val){
            if($val['service_requisition_status']==4) {
                $date       = date('Y-m-d H:i:s');
                $position   = "Selesai Review";
            } else if($val['service_requisition_status']==3){
                $date       = $this->getServiceDispositionApprovedDate($val['service_requisition_id']);
                $position   = "Persetujuan Disposisi di ".$this->getSectionName($val['service_requisition_id']);
            } else if($val['service_requisition_status']==1){
                $date       = $this->getServiceDispositionDate($val['service_requisition_id']);
                $position   = "Disposisi di ".$this->getSectionName($val['service_requisition_id']);
            }else {
                $date       = $val['created_at'];
                $position   = "Pengajuan Bantuan";
            }
            $date1      = new DateTime();
            $date2      = new DateTime($date);
            $interval   = $date1->diff($date2);

            $service_name = CoreService::where('service_id', $val['service_id'])
            ->first()
            ->service_name;

            $graphstuckarray[$key]['service_requisition_no']     = $val['service_requisition_no'];
            $graphstuckarray[$key]['service_requisition_name']   = $val['service_requisition_name'];
            $graphstuckarray[$key]['service_name']               = $service_name;
            $graphstuckarray[$key]['interval']                   = $interval->days.".".$interval->h;

            $graphsectionarray[$key]['service_requisition_no']   = $val['service_requisition_no'];
            $graphsectionarray[$key]['position']                 = $position;

            $graphservicearray[$key]['service_requisition_no']   = $val['service_requisition_no'];
            $graphservicearray[$key]['service_name']             = $service_name;
        }

        //Grafik 5 paling lama
        usort($graphstuckarray, array($this, "usort_callback"));
        $graphstuck = array_slice($graphstuckarray, 0, 5);
        foreach($graphstuck as $key => $val){
            list($days, $hours) = explode('.', $val['interval']);
            $graphstuck[$key]['interval_name'] = $days.' Hari '.$hours.' Jam';
            $graphstuck[$key]['interval'] = intval($days);
        }

        //Grafil per Posisi
        $graphsection = array();
        foreach ($graphsectionarray as $key => $val) {
        $name = strlen($val['position']) > 20 ? substr($val['position'],0,20)."..." : $val['position'];
        if (isset($graphsection[$name])) {            
                $graphsection[$name]++;
            } else {
                $graphsection[$name] = 1;
            }
        }

        //Grafik per layanan
        $graphservice = array();
        foreach ($graphservicearray as $key => $val) {
        $name = strlen($val['service_name']) > 20 ? substr($val['service_name'],0,20)."..." : $val['service_name'];
        if (isset($graphservice[$name])) {            
                $graphservice[$name]++;
            } else {
                $graphservice[$name] = 1;
            }
        }

        $data['graphsection']   = $graphsection;
        $data['graphservice']   = $graphservice;
        $data['graphstuck']     = $graphstuck;

        return response([
            'data' => $data
        ],201);
    }

    public function getTransServiceDisposition(){
        $transservicedisposition = TransServiceDisposition::select('trans_service_disposition.*', 'core_service.service_name')
        ->join('core_service', 'core_service.service_id', 'trans_service_disposition.service_id')
        ->where('trans_service_disposition.data_state', 0)
        ->where('approved_status', 1)
        ->where('review_status', 0)
        ->get();

        return response([
            'data' => $transservicedisposition
        ],201);
    }

    public function getTransServiceGeneral(){
        $transservicegeneral = TransServiceGeneral::select('trans_service_general.*', 'core_service_general_priority.service_general_priority_name')
        ->join('core_service_general_priority', 'core_service_general_priority.service_general_priority_id', 'trans_service_general.service_general_priority')
        ->where('trans_service_general.service_general_status', 0)
        ->where('trans_service_general.data_state', 0)
        ->get();

        return response([
            'data' => $transservicegeneral
        ],201);
    }

    public function getServiceDispositionReviewDate($service_requisition_id){
        $transservicedisposition = TransServiceDisposition::where('data_state', 0)
        ->where('service_requisition_id', $service_requisition_id)
        ->first();

        return $transservicedisposition['review_at'];
    }

    public function getServiceDispositionApprovedDate($service_requisition_id){
        $transservicedisposition = TransServiceDisposition::where('data_state', 0)
        ->where('service_requisition_id', $service_requisition_id)
        ->first();

        return $transservicedisposition['approved_at'];
    }

    public function getSectionName($service_requisition_id){
        $servicedisposition = TransServiceDisposition::where('data_state', 0)
        ->where('service_requisition_id', $service_requisition_id)
        ->first();

        if($servicedisposition){
            $section = CoreSection::where('data_state', 0)
            ->where('section_id', $servicedisposition['section_id'])
            ->first();
        }else{
            $section = CoreSection::where('data_state', 0)
            ->where('section_id', 1)
            ->first();
        }
        
        return $section['section_name'];
    }

    public function getServiceDispositionDate($service_requisition_id){
        $transservicedisposition = TransServiceDisposition::where('data_state', 0)
        ->where('service_requisition_id', $service_requisition_id)
        ->first();

        return $transservicedisposition['created_at'];
    }

    public function getTransServiceDispositionDetail(Request $request){
        $servicedisposition = TransServiceDisposition::select('trans_service_disposition.service_disposition_id', 'trans_service_disposition.service_requisition_id', 'trans_service_disposition.service_requisition_name', 'trans_service_disposition.service_requisition_no', 'trans_service_disposition.service_disposition_remark', 'trans_service_disposition.service_id', 'core_service.service_name')
        ->join('core_service', 'core_service.service_id', '=', 'trans_service_disposition.service_id')
        ->where('trans_service_disposition.data_state', 0)
        ->where('trans_service_disposition.service_disposition_id', $request['service_disposition_id'])
        ->first();
        
        $mustahikworksheetresult = MustahikWorksheetResult::select('mustahik_worksheet_result.worksheet_result_data', 'mustahik_worksheet_result.worksheet_result_date', 'mustahik_worksheet_result.user_id', 'mustahik_worksheet_requisition.service_id', 'mustahik_worksheet_requisition.worksheet_requisition_id')
        ->join('mustahik_worksheet_requisition', 'mustahik_worksheet_requisition.worksheet_requisition_id', '=', 'mustahik_worksheet_result.worksheet_requisition_id')
        ->where('mustahik_worksheet_requisition.service_requisition_id', $servicedisposition['service_requisition_id'])
        ->first();

        $score = 0;
        if($mustahikworksheetresult){
            $mustahikworksheet = MustahikWorksheet::select('worksheet_id', 'worksheet_no', 'worksheet_name', 'worksheet_type', 'worksheet_code')
            ->where('service_id', $mustahikworksheetresult['service_id'])
            ->get();

            $worksheetresultdata = json_decode($mustahikworksheetresult['worksheet_result_data']);

            $scoringdata = array();

            foreach($mustahikworksheet as $key => $val){
                if($val['worksheet_type'] == 1 || $val['worksheet_type'] == 3 || $val['worksheet_type'] == 4){
                    $scoringdata[$val['worksheet_code']] = '';
                }else if($val['worksheet_type'] == 2){
                    $scoringdata[$val['worksheet_code']] = array();
                    $mustahikworksheetitem = MustahikWorksheetItem::select('worksheet_item_id', 'worksheet_id', 'section_name', 'worksheet_item_name', 'worksheet_item_code')
                    ->where('worksheet_id', $val['worksheet_id'])
                    ->get();

                    foreach($mustahikworksheetitem as $keyy => $vall){
                        $scoringdata[$val['worksheet_code']][$vall['worksheet_item_code']] = '';
                    }
                }
            }

            foreach($scoringdata as $key => $val){
                foreach($worksheetresultdata as $keyy => $vall){
                    if($key == $vall->worksheet_code){
                        if(isset($vall->value)){
                            $scoringdata[$key] = $vall->value;
                        }else if(isset($vall->worksheetitem)){
                            foreach($vall->worksheetitem as $keyyy => $valll){
                                $scoringdata[$key][$valll->worksheet_item_code] = $valll->value;
                            }
                        }
                    }
                }
            }

            if($mustahikworksheetresult['service_id'] == 7){
                if($scoringdata['worksheet_home_size_type'] >= 41 && $scoringdata['worksheet_home_size_type'] <= 60) {
                    $score +=1;
                } 
                if($scoringdata['worksheet_home_size_type'] >= 61 && $scoringdata['worksheet_home_size_type'] <= 100) {
                    $score +=2;
                }
                if($scoringdata['worksheet_home_size_type'] > 100) {
                    $score +=4;
                } 
                if($scoringdata['worksheet_wall']['wall_wood']) {
                    $score +=3;
                } 
                if($scoringdata['worksheet_wall']['wall_mix']) {
                    $score +=4;
                } 
                if($scoringdata['worksheet_wall']['wall_plaster']) {
                    $score +=5;
                }
                if($scoringdata['worksheet_floor']['floor_wood']) {
                    $score +=2;
                } 
                if($scoringdata['worksheet_floor']['floor_cement']) {
                    $score +=4;
                } 
                if($scoringdata['worksheet_floor']['floor_ceramic']) {
                    $score +=5;
                } 
                if($scoringdata['worksheet_roof']['roof_asbes']) {
                    $score +=1;
                } 
                if($scoringdata['worksheet_roof']['roof_metal']) {
                    $score +=3;
                } 
                if($scoringdata['worksheet_roof']['roof_tile']) {
                    $score +=5;
                } 
                if($scoringdata['worksheet_sanitation']['sanitation_bath_room']) {
                    $score +=3;
                } 
                if($scoringdata['worksheet_sanitation']['sanitation_mck']) {
                    $score +=3;
                } 
                if($scoringdata['worksheet_sanitation']['sanitation_well']) {
                    $score +=3;
                } 
                if($scoringdata['worksheet_electricity']['electricity_private']) {
                    $score +=2;
                } 
                if($scoringdata['worksheet_electricity']['electricity_connect']) {
                    $score +=5;
                } 
                if($scoringdata['worksheet_ownership']['ownership_rent']) {
                    $score +=5;
                } 
                if($scoringdata['worksheet_ownership']['ownership_family']) {
                    $score +=3;
                }

                $penghasilan = 0; 
                if($scoringdata['worksheet_husband_business_yearly']){
                    $penghasilan += $scoringdata['worksheet_husband_business_yearly'];
                }
                if($scoringdata['worksheet_wife_business_yearly']){
                    $penghasilan += $scoringdata['worksheet_wife_business_yearly'];
                }
                if($scoringdata['worksheet_parents_yearly']){
                    $penghasilan += $scoringdata['worksheet_parents_yearly'];
                }
                if($scoringdata['worksheet_childs_yearly']){
                    $penghasilan += $scoringdata['worksheet_childs_yearly'];
                }
                if($scoringdata['worksheet_other_yearly']){
                    $penghasilan += $scoringdata['worksheet_other_yearly'];
                }

                if($penghasilan >= 5000001 && $penghasilan <= 10000000) {
                    $score +=2;
                } 
                if($penghasilan >= 10000001 && $penghasilan <= 15000000) {
                    $score +=4;
                } 
                if($penghasilan >= 15000001) {
                    $score +=5;
                } 

                if($score <= 20){
                    $scoringresult = "Approve";
                }else if($score > 20 && $score < 40){
                    $scoringresult = "Komite";
                }else if($score >= 40){
                    $scoringresult = "Reject";
                }
            }else if($mustahikworksheetresult['service_id'] == 1){
                if($scoringdata['mosque_poor_KK'] < 5) {
                    $score += 4;
                }
                if($scoringdata['mosque_poor_KK'] >= 5 && $scoringdata['mosque_poor_KK'] <= 10) {
                    $score += 3;
                }
                if($scoringdata['mosque_poor_KK'] >= 11 && $scoringdata['mosque_poor_KK'] <= 15) {
                    $score += 2;
                }
                if($scoringdata['mosque_poor_KK'] > 15) {
                    $score += 1;
                }
                if($scoringdata['mosque_wakaf_certificate']['no_certificate']) {
                    $score += 4;
                }
                if($scoringdata['mosque_wakaf_certificate']['certificate']) {
                    $score += 2;
                }
                if($scoringdata['mosque_permission']['no_permission']) {
                    $score += 4;
                }
                if($scoringdata['mosque_permission']['permission']) {
                    $score += 2;
                }
                if($scoringdata['mosque_jamaah'] < 50) {
                    $score += 4;
                }
                if($scoringdata['mosque_jamaah'] >= 50 && $scoringdata['mosque_jamaah'] <= 100) {
                    $score += 3;
                }
                if($scoringdata['mosque_jamaah'] >= 101 && $scoringdata['mosque_jamaah'] <= 150) {
                    $score += 2;
                }
                if($scoringdata['mosque_jamaah'] > 150) {
                    $score += 1;
                }
                if($scoringdata['mosque_area'] < 50) {
                    $score += 4;
                }
                if($scoringdata['mosque_area'] >= 50 && $scoringdata['mosque_area'] <= 100) {
                    $score += 3;
                }
                if($scoringdata['mosque_area'] >= 101 && $scoringdata['mosque_area'] <= 150) {
                    $score += 2;
                }
                if($scoringdata['mosque_area'] > 150) {
                    $score += 1;
                }
                if($scoringdata['water_source']['water_source_machine']) {
                    $score += 4;
                }
                if($scoringdata['water_source']['water_source_manual']) {
                    $score += 2;
                }
                if($scoringdata['floor']['floor_1']) {
                    $score += 1;
                }
                if($scoringdata['floor']['floor_2']) {
                    $score += 3;
                }
                if($scoringdata['floor']['floor_3']) {
                    $score += 5;
                }
                if($scoringdata['wall']['wall_1']) {
                    $score += 4;
                }
                if($scoringdata['wall']['wall_2']) {
                    $score += 2;
                }
                if($scoringdata['roof']['roof_1']) {
                    $score += 4;
                }
                if($scoringdata['roof']['roof_2']) {
                    $score += 2;
                }
                if($scoringdata['roof']['roof_3']) {
                    $score += 1;
                }
                if($scoringdata['fence']['fence']) {
                    $score += 4;
                }
                if($scoringdata['fence']['no_fence']) {
                    $score += 2;
                }
                if($scoringdata['sarpras']['mat']) {
                    $score += 1;
                }
                if($scoringdata['sarpras']['ac']) {
                    $score += 2;
                }
                if($scoringdata['sarpras']['fan']) {
                    $score += 1;
                }
                if($scoringdata['sarpras']['sound']) {
                    $score += 1;
                }
                if($scoringdata['sarpras']['wudhu']) {
                    $score += 1;
                }
                if($scoringdata['sarpras']['parking']) {
                    $score += 1;
                } 
                if($scoringdata['sarpras']['canopy']) {
                    $score += 1;
                } 
                
                if($score < 25){
                    $scoringresult = "Aprrove";
                }else if($score >= 25 && $score <= 35){
                    $scoringresult = "Komite";
                }else if($score > 35){
                    $scoringresult = "Reject";
                } 
            }else if($mustahikworksheetresult['service_id'] == 6){
                if($scoringdata['business_location']['location_not_strategic'] == true) {
                    $score += 2;
                }
                if($scoringdata['business_location']['location_strategic'] == true) {
                    $score += 4;
                }
                if($scoringdata['business_community']['community_group']) {
                    $score += 2;
                }
                if($scoringdata['business_community']['community_individual']) {
                    $score += 4;
                }
                if(!$scoringdata['business_age'] || $scoringdata['business_age'] < 6) {
                    $score += 1;
                }
                if($scoringdata['business_age'] >= 6 && $scoringdata['business_age'] <= 12) {
                    $score += 2;
                }
                if($scoringdata['business_age'] >= 13 && $scoringdata['business_age'] <= 24) {
                    $score += 3;
                }
                if($scoringdata['business_age'] > 24) {
                    $score += 4;
                }
                if($scoringdata['business_employee'] <= 0) {
                    $score += 1;
                }
                if($scoringdata['business_employee'] >= 1 && $scoringdata['business_employee'] <= 3) {
                    $score += 2;
                }
                if($scoringdata['business_employee'] >= 4 && $scoringdata['business_employee'] <= 10) {
                    $score += 3;
                }
                if($scoringdata['business_employee'] > 10) {
                    $score += 4;
                }
                if($scoringdata['business_interest'] < 8) {
                    $score += 1;
                }
                if($scoringdata['business_interest'] >= 8 && $scoringdata['business_interest'] <= 10) {
                    $score += 2;
                }
                if($scoringdata['business_interest'] >= 11 && $scoringdata['business_interest'] <= 15) {
                    $score += 3;
                }
                if($scoringdata['business_interest'] >= 16 && $scoringdata['business_interest'] <= 20) {
                    $score += 4;
                }
                if($scoringdata['business_interest'] > 20) {
                    $score += 5;
                }
                if($scoringdata['business_assistance_loans']['assistance_loans'] == true) {
                    $score += 1;
                }
                if($scoringdata['business_assistance_loans']['loans'] == true) {
                    $score += 2;
                }
                if($scoringdata['business_assistance_loans']['assistance'] == true) {
                    $score += 3;
                }
                if($scoringdata['business_assistance_loans']['no_assistance_loans'] == true) {
                    $score += 4;
                }
                if($scoringdata['category']['asnaf_poor'] == true){
                    if($scoringdata['sktm']['no_sktm'] == true) {
                        $score += 2;
                    }
                    if($scoringdata['sktm']['sktm'] == true) {
                        $score += 4;
                    }
                }

                if($score >= 20){
                    $scoringresult = "Aprrove";
                }else if($score >= 9 && $score <= 19){
                    $scoringresult = "Komite";
                }else if($score <= 8){
                    $scoringresult = "Reject";
                } 
            }else{
                $scoringresult = null;
            }
        }else{
            $scoringresult = null;
        }

        $data = array (
            'servicedisposition' => $servicedisposition,
            'scoringresult'      => $scoringresult,
            'score'              => $score,
        );

        return response([
            'data' => $data
        ],201);
    }

    public function serviceDispositionDisproval(Request $request){
        $fields = $request->validate([
            'user_id'                   => 'required',
            'service_disposition_id'    => 'required',
            'disapprove_remark'         => 'required',
        ]);

        $servicedisposition = TransServiceDisposition::findOrFail($fields['service_disposition_id']);
        $servicedisposition->review_status     = 2;
        $servicedisposition->disapprove_id     = $fields['user_id'];
        $servicedisposition->disapprove_at     = date('Y-m-d H:i:s');
        $servicedisposition->disapprove_remark = $fields['disapprove_remark'];
            
        $service_requisition_id = $servicedisposition['service_requisition_id'];
        $servicerequisition     = TransServiceRequisition::where('data_state', 0)
        ->where('service_requisition_id', $service_requisition_id)
        ->first(); 
        
        if($servicedisposition->save()){
            $servicerequisition->service_requisition_status = 5;
            if($servicerequisition->save()){  
            }else{
                return response([
                    'message' => 'Simpan Data Tidak Berhasil'
                ],401);
            }
        
            $disposition_data = TransServiceDisposition::findOrFail($fields['service_disposition_id']);

            $service_log = array(
                'service_status'            => 9,
                'service_requisition_no'    => $disposition_data['service_requisition_no'],
                'section_id'                => $disposition_data['section_id'],
                'created_id'                => $fields['user_id'],
            );
            TransServiceLog::create($service_log);

            $wa_msg = "Siabas\r\n\r\n\r\nNama : ".$disposition_data['service_requisition_name']."\r\n\r\nNomor Pengajuan : ".$disposition_data['service_requisition_no']."\r\n\r\nJenis Pengajuan : ".$this->getServiceName($disposition_data['service_id'])."\r\n\r\nBagian : ".$this->getSectionName(1)."\r\n\r\nPesan : ".$this->getMessage(8);
            $wa_status = $this->getMessageStatus(8);
            $wa_no  = $disposition_data['service_requisition_phone'];
            $this->postWhatsappMessages($wa_msg, $wa_status, $wa_no);

            return response([
                'message' => 'Simpan Data Berhasil'
            ],201);
        }else{
            return response([
                'message' => 'Simpan Data Tidak Berhasil'
            ],401);
        }
    }

    public function serviceDispositionApproval(Request $request){
        $fields = $request->validate([
            'user_id'                   => 'required',
            'service_disposition_id'    => 'required',
            'review_remark'             => 'required',
            'file_sk'                   => 'required',
        ]);
        
        $fileNameToStore = '';

        if($request->hasFile('file_sk')){
            $filenameWithExt    = $request->file('file_sk')->getClientOriginalName();
            $filename           = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension          = $request->file('file_sk')->getClientOriginalExtension();
            $fileNameToStore    = $filename.'_'.time().'.'.$extension;
            $path               = $request->file('file_sk')->storeAs('public/sk/'.$fields['service_disposition_id'],$fileNameToStore);
        }else{
            return response([
                'message' => 'Simpan Data Tidak Berhasil'
            ],401);
        }

        $servicedisposition = TransServiceDisposition::findOrFail($fields['service_disposition_id']);
        $servicedisposition->file_sk       = $fileNameToStore;
        $servicedisposition->review_status = 1;
        $servicedisposition->review_id     = $fields['user_id'];
        $servicedisposition->review_at     = date('Y-m-d H:i:s');
        $servicedisposition->review_remark = $fields['review_remark'];
            
        $service_requisition_id = $servicedisposition['service_requisition_id'];
        $servicerequisition     = TransServiceRequisition::where('data_state', 0)
        ->where('service_requisition_id', $service_requisition_id)
        ->first(); 

        if($servicedisposition->save()){
            $servicerequisition->service_requisition_status = 4;
            if($servicerequisition->save()){  
            }else{
                return response([
                    'message' => 'Simpan Data Tidak Berhasil'
                ],401);
            }
        
            $disposition_data = TransServiceDisposition::findOrFail($fields['service_disposition_id']);

            $service_log = array(
                'service_status'            => 4,
                'service_requisition_no'    => $disposition_data['service_requisition_no'],
                'section_id'                => $disposition_data['section_id'],
                'created_id'                => $fields['user_id'],
            );
            TransServiceLog::create($service_log);

            $wa_msg = "Siabas\r\n\r\n\r\nNama : ".$disposition_data['service_requisition_name']."\r\n\r\nNomor Pengajuan : ".$disposition_data['service_requisition_no']."\r\n\r\nJenis Pengajuan : ".$this->getServiceName($disposition_data['service_id'])."\r\n\r\nBagian : ".$this->getSectionName($disposition_data['section_id'])."\r\n\r\nPesan : ".$this->getMessage(4);
            $wa_status = $this->getMessageStatus(4);
            $wa_no  = $disposition_data['service_requisition_phone'];
            $this->postWhatsappMessages($wa_msg, $wa_status, $wa_no);

            return response([
                'message' => 'Simpan Data Berhasil'
            ],201);
        }else{
            return response([
                'message' => 'Simpan Data Tidak Berhasil'
            ],401);
        }
    }

    public function getTransServiceGeneralDetail(Request $request){
        $servicegeneral = TransServiceGeneral::select('trans_service_general.service_general_id', 'trans_service_general.service_general_no', 'trans_service_general.service_general_agency', 'trans_service_general.service_general_phone', 'trans_service_general.service_general_file', 'core_service_general_priority.service_general_priority_name')
        ->join('core_service_general_priority', 'core_service_general_priority.service_general_priority_id', '=', 'trans_service_general.service_general_priority')
        ->where('trans_service_general.service_general_id', $request['service_general_id'])
        ->first();

        return response([
            'data' => $servicegeneral
        ],201);
    }

    public function serviceGeneralDisproval(Request $request){
        $fields = $request->validate([
            'user_id'                    => 'required',
            'service_general_id'         => 'required',
            'service_general_remark'     => 'required',
        ]);
        
        $servicegeneral = TransServiceGeneral::findOrFail($fields['service_general_id']);
        $servicegeneral->service_general_remark     = $fields['service_general_remark'];
        $servicegeneral->service_general_status     = 2;
        $servicegeneral->disapproved_id             = $fields['user_id'];
        $servicegeneral->disapproved_at             = date('Y-m-d H:i:s');

        if($servicegeneral->save()){
            $wa_msg = "Siabas\r\n\r\n\r\nNama Instansi: ".$servicegeneral['service_general_agency']."\r\n\r\nNomor Pengajuan : ".$servicegeneral['service_general_no']."\r\n\r\nBagian : Surat Umum\r\n\r\nPesan : ".$this->getMessage(8);
            $wa_status = $this->getMessageStatus(8);
            $wa_no  = $servicegeneral['service_general_phone'];
            $this->postWhatsappMessages($wa_msg, $wa_status, $wa_no);
            
            return response([
                'message' => 'Simpan Data Berhasil'
            ],201);
        }else{
            return response([
                'message' => 'Simpan Data Tidak Berhasil'
            ],401);
        }
    }

    public function serviceGeneralApproval(Request $request){
        $fields = $request->validate([
            'user_id'                    => 'required',
            'service_general_id'         => 'required',
            'service_general_remark'     => 'required',
        ]);
        
        $fileNameToStore = '';

        if($request->hasFile('file_sk')){
            $filenameWithExt    = $request->file('file_sk')->getClientOriginalName();
            $filename           = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension          = $request->file('file_sk')->getClientOriginalExtension();
            $fileNameToStore    = $filename.'_'.time().'.'.$extension;
            $path               = $request->file('file_sk')->storeAs('public/service-general',$fileNameToStore);

        }else{
            return response([
                'message' => 'Simpan Data Tidak Berhasil'
            ],401);
        }

        $servicegeneral                             = TransServiceGeneral::findOrFail($fields['service_general_id']);
        $servicegeneral->service_general_sk_file    = $fileNameToStore;
        $servicegeneral->service_general_status     = 1;
        $servicegeneral->service_general_remark     = $fields['service_general_remark'];
        $servicegeneral->approved_id                = $fields['user_id'];
        $servicegeneral->approved_at                = date('Y-m-d H:i:s');

        if($servicegeneral->save()){
            $wa_msg     = "Siabas\r\n\r\n\r\nNama Instansi: ".$servicegeneral['service_general_agency']."\r\n\r\nNomor Pengajuan : ".$servicegeneral['service_general_no']."\r\n\r\nBagian : Surat Umum\r\n\r\nPesan : ".$this->getMessage(4);
            $wa_status  = $this->getMessageStatus(4);
            $wa_no      = $servicegeneral['service_general_phone'];
            $this->postWhatsappMessages($wa_msg, $wa_status, $wa_no);

            return response([
                'message' => 'Simpan Data Berhasil'
            ],201);
        }else{
            return response([
                'message' => 'Simpan Data Tidak Berhasil'
            ],401);
        }
    }

    public function getServiceName($service_id){
        $service = CoreService::where('data_state', 0)
        ->where('service_id', $service_id)
        ->first();

        return $service['service_name'];
    }
}
