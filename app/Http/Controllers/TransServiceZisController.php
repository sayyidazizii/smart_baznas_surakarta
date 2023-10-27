<?php

namespace App\Http\Controllers;

use App\Models\CoreKecamatan;
use App\Models\CoreKelurahan;
use App\Models\CoreService;
use App\Models\TransServiceZis;
use App\Models\TransServiceZisItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class TransServiceZisController extends Controller
{
    public function index()
    {
        Session::forget('datatransservicezisItem');
        Session::forget('data_transservicezis');

        if (!Session::get('start_date')) {
            $start_date     = date('Y-m-d');
        } else {
            $start_date = Session::get('start_date');
        }

        if (!Session::get('end_date')) {
            $end_date     = date('Y-m-d');
            $stop_date = date('Y-m-d', strtotime($end_date . ' +1 day'));
        } else {
            $end_date = Session::get('end_date');
            $stop_date = date('Y-m-d', strtotime($end_date . ' +1 day'));
        }

        $transservicezis = TransServiceZis::where('data_state', 0)
            ->where('created_at', '>=', $start_date)
            ->where('created_at', '<=', $stop_date)
            ->get();
        // dd($transservicezis);
        return view('content/TransServiceZis/ListTransServiceZis', compact('transservicezis', 'start_date', 'end_date'));
    }


    public function filter(Request $request)
    {
        $start_date     = $request->start_date;
        $end_date       = $request->end_date;

        Session::put('start_date', $start_date);
        Session::put('end_date', $end_date);

        return redirect('/trans-service-zis');
    }

    //form add zakat infaq sedekah
    public function addTransServicezis()
    {
        $service_zis_type = array(
            1 => 'Instansi',
            2 => 'Perorangan',
        );
        $service_zis_category = array(
            1 => 'Zakat',
            2 => 'infaq',
            3 => 'Sedekah',
        );
        $service_zis_item_type = array(
            1 => 'Uang',
            2 => 'Barang',
        );

        $kelurahan = CoreKelurahan::where('data_state', 0)->pluck('kelurahan_name', 'kelurahan_id');
        $kecamatan = CoreKecamatan::where('data_state', 0)->pluck('kecamatan_name', 'kecamatan_id');
        // dd($kecamatan);

        $transservicezisItem        = Session::get('datatransservicezisItem');
        $transservicezis            = Session::get('data_transservicezis');
        return view('content/TransServiceZis/FormAddTransServiceZis', compact('service_zis_category', 'service_zis_type', 'service_zis_item_type', 'transservicezisItem', 'transservicezis', 'kelurahan', 'kecamatan'));
    }

    //form detail zakat infaq sedekah
    public function detailTransServicezis($service_zis_id)
    {
        $transservicezis = TransServiceZis::where('data_state', 0)
            ->where('service_zis_id', $service_zis_id)
            ->first();

        $transservicezisItem = TransServiceZisItem::where('service_zis_id', $service_zis_id)
            ->get();

        return view('content/TransServiceZis/FormDetailTransServiceZis', compact('transservicezis', 'transservicezisItem', 'service_zis_id'));
    }

    //process add array item zakat infaq sedekah
    public function processAddItemArray(Request $request)
    {
        $transservicezisItem = array(
            'service_zis_item_type'         => $request->service_zis_item_type,
            'service_zis_item_amount'       => $request->service_zis_item_amount,
            'service_zis_item_remark'       => $request->service_zis_item_remark,
        );

        $lasttransservicezisItem        = Session::get('datatransservicezisItem');
        if ($lasttransservicezisItem !== null) {
            array_push($lasttransservicezisItem, $transservicezisItem);
            Session::put('datatransservicezisItem', $lasttransservicezisItem);
        } else {
            $lasttransservicezisItem = [];
            array_push($lasttransservicezisItem, $transservicezisItem);
            Session::push('datatransservicezisItem', $transservicezisItem);
        }
    }

    //add element 
    public function addElementsServiceZis(Request $request)
    {
        $data_transservicezis            = Session::get('data_transservicezis');
        if (!$data_transservicezis || $data_transservicezis == '') {
            $data_transservicezis['service_zis_date'] = '';
            $data_transservicezis['service_zis_type'] = '';
            $data_transservicezis['service_zis_category'] = '';
            $data_transservicezis['service_zis_name'] = '';
            $data_transservicezis['service_zis_phone'] = '';
            $data_transservicezis['service_zis_address'] = '';
            $data_transservicezis['kecamatan_id'] = '';
            $data_transservicezis['kelurahan_id'] = '';
        }
        $data_transservicezis[$request->name] = $request->value;
        Session::put('data_transservicezis', $data_transservicezis);

        return redirect('/trans-service-zis/add');
    }

    //process add zakat infaq sedekah
    public function processAddTransServicezis(Request $request)
    {

        $allrequest = $request->all();
        // dd($allrequest);
        $fields = $request->validate([
            'service_zis_date'              => 'required',
            'service_zis_type'              => 'required',
            'service_zis_category'          => 'required',
            'service_zis_name'              => 'required',
            'service_zis_phone'             => 'required',
            'service_zis_address'           => 'required',
        ]);

        $transservicezis = array(
            'service_zis_date'           => $fields['service_zis_date'],
            'service_zis_type'           => $fields['service_zis_type'],
            'service_zis_category'       => $fields['service_zis_category'],
            'service_zis_name'           => $fields['service_zis_name'],
            'service_zis_phone'          => $fields['service_zis_phone'],
            'service_zis_address'        => $fields['service_zis_address'],
            'kelurahan_id'               => $request['kelurahan_id'],
            'kecamatan_id'               => $request['kecamatan_id'],
            'created_id'                 => Auth::id(),
        );
        //dd($purchaseorder);
        if (TransServiceZis::create($transservicezis)) {
            $transservicezis_id = TransServiceZis::orderBy('created_at', 'DESC')->first();
            $transservicezisItem = Session::get('datatransservicezisItem');
            foreach ($transservicezisItem as $key => $val) {
                $datatransservicezisItem = array(
                    'service_zis_id'                => $transservicezis_id['service_zis_id'],
                    'service_zis_item_type'         => $val['service_zis_item_type'],
                    'service_zis_item_amount'       => $val['service_zis_item_amount'],
                    'service_zis_item_remark'       => $val['service_zis_item_remark'],
                );
                //dd($datapurchaseorderitem);
                TransServiceZisItem::create($datatransservicezisItem);
            }
            $msg = 'Tambah Zakat Infaq Sedekah Berhasil';
            return redirect('/trans-service-zis')->with('msg', $msg);
        } else {
            $msg = 'Tambah Zakat Infaq Sedekah Gagal';
            return redirect('/trans-service-zis/add')->with('msg', $msg);
        }
        // dd($transservicezisItem);
    }

    //process delete zakat infaq sedekah
    public function deleteTransServicezis(Request $request)
    {
        $journalvoucheritem = TransServiceZis::where('service_zis_id', $request->service_zis_id)->first();
        $journalvoucheritem->data_state = 1;

        if ($journalvoucheritem->save()) {
            $msg = 'Hapus Zakat Infaq Sedekah Berhasil';
            return redirect('/trans-service-zis')->with('msg', $msg);
        } else {
            $msg = 'Hapus Zakat Infaq Sedekah Gagal';
            return redirect('/trans-service-zis')->with('msg', $msg);
        }
    }

    //delete item array
    public function deleteItemArray($record_id)
    {
        $arrayBaru            = array();
        $dataArrayHeader    = Session::get('datatransservicezisItem');

        foreach ($dataArrayHeader as $key => $val) {
            if ($key != $record_id) {
                $arrayBaru[$key] = $val;
            }
        }
        Session::forget('datatransservicezisItem');
        Session::put('datatransservicezisItem', $arrayBaru);

        return redirect('/trans-service-zis/add/');
    }

    //form edit zakat infaq sedekah
    public function editTransServicezis($service_zis_id)
    {

        $service_zis_type = array(
            1 => 'Instansi',
            2 => 'Perorangan',
        );
        $service_zis_category = array(
            1 => 'Zakat',
            2 => 'infaq',
            3 => 'Sedekah',
        );
        $service_zis_item_type = array(
            1 => 'Uang',
            2 => 'Barang',
        );

        $transservicezis = TransServiceZis::where('data_state', 0)
            ->where('service_zis_id', $service_zis_id)
            ->first();

        $transservicezisItem = TransServiceZisItem::where('service_zis_id', $service_zis_id)
            ->get();
        $kelurahan = CoreKelurahan::where('data_state', 0)->pluck('kelurahan_name', 'kelurahan_id');
        $kecamatan = CoreKecamatan::where('data_state', 0)->pluck('kecamatan_name', 'kecamatan_id');
        return view('content/TransServiceZis/FormEditTransServiceZis', compact('service_zis_category', 'kecamatan', 'kelurahan', 'transservicezis', 'transservicezisItem', 'service_zis_id', 'service_zis_type', 'service_zis_item_type'));
    }

    //process edit zakat infaq sedekah
    public function processEditTransServicezis(Request $request)
    {

        $allrequest = $request->all();
        // dd($allrequest);
        $transservicezis = TransServiceZis::find($request->service_zis_id);
        $transservicezis->service_zis_type      = $request->service_zis_type;
        $transservicezis->service_zis_category  = $request->service_zis_category;
        $transservicezis->service_zis_name      = $request->service_zis_name;
        $transservicezis->service_zis_phone     = $request->service_zis_phone;
        $transservicezis->service_zis_address   = $request->service_zis_address;
        $transservicezis->kelurahan_id          = $request->kelurahan_id;
        $transservicezis->kecamatan_id          = $request->kecamatan_id;
        if ($transservicezis->save()) {
            $msg = 'Edit Zakat Infaq Sedekah Berhasil';
            return redirect('/trans-service-zis')->with('msg', $msg);
        } else {
            $msg = 'Edit Zakat Infaq Sedekah Gagal';
            return redirect('/trans-service-zis')->with('msg', $msg);
        }
    }


    //export excel
    public function export()
    {

        Session::forget('datatransservicezisItem');
        Session::forget('data_transservicezis');

        if (!Session::get('start_date')) {
            $start_date     = date('Y-m-d');
        } else {
            $start_date = Session::get('start_date');
        }

        if (!Session::get('end_date')) {
            $end_date     = date('Y-m-d');
            $stop_date = date('Y-m-d', strtotime($end_date . ' +1 day'));
        } else {
            $end_date = Session::get('end_date');
            $stop_date = date('Y-m-d', strtotime($end_date . ' +1 day'));
        }

        $transservicezis = TransServiceZis::select('*')
            ->join('trans_service_zis_item', 'trans_service_zis_item.service_zis_id', 'trans_service_zis.service_zis_id')
            ->where('trans_service_zis.data_state', 0)
            ->where('trans_service_zis.created_at', '>=', $start_date)
            ->where('trans_service_zis.created_at', '<=', $stop_date)
            ->get();
        // dd($transservicezis);
        $spreadsheet = new Spreadsheet();

        if (count($transservicezis) >= 0) {
            $spreadsheet->getProperties()->setCreator("SYSTEM INFORMATION")
                ->setLastModifiedBy("SYSTEM INFORMATION")
                ->setTitle("Rekap Zakat Infaq Sedekah")
                ->setSubject("")
                ->setDescription("Rekap Zakat Infaq Sedekah")
                ->setKeywords("Rekap Zakat Infaq Sedekah")
                ->setCategory("Rekap Zakat Infaq Sedekah");

            $sheet = $spreadsheet->getActiveSheet(0);
            $spreadsheet->getActiveSheet()->setTitle("Rekap Zakat Infaq Sedekah");
            $spreadsheet->getActiveSheet()->getPageSetup()->setFitToWidth(1);
            $spreadsheet->getActiveSheet()->getPageSetup()->setFitToWidth(1);
            $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(5);
            $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(25);
            $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(20);
            $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(20);
            $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(10);
            $spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(25);
            $spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(20);
            $spreadsheet->getActiveSheet()->getColumnDimension('J')->setWidth(20);
            $spreadsheet->getActiveSheet()->getColumnDimension('K')->setWidth(20);
            $spreadsheet->getActiveSheet()->getColumnDimension('L')->setWidth(20);
            $spreadsheet->getActiveSheet()->getColumnDimension('M')->setWidth(20);
            $spreadsheet->getActiveSheet()->getColumnDimension('N')->setWidth(20);




            $spreadsheet->getActiveSheet()->mergeCells("B1:N1");
            $spreadsheet->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->getStyle('B1')->getFont()->setBold(true)->setSize(16);

            $spreadsheet->getActiveSheet()->getStyle('B3:N3')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $spreadsheet->getActiveSheet()->getStyle('B3:N3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $sheet->setCellValue('B1', "Rekap Zakat Infaq Sedekah Periode " . date('d M Y'));
            $sheet->setCellValue('B3', "No");
            $sheet->setCellValue('C3', "Jenis");
            $sheet->setCellValue('D3', "Kategori");
            $sheet->setCellValue('E3', "Tanggal");
            $sheet->setCellValue('F3', "Nama");
            $sheet->setCellValue('G3', "Kelurahan");
            $sheet->setCellValue('H3', "Kecamatan");
            $sheet->setCellValue('I3', "Alamat");
            $sheet->setCellValue('J3', "Nomor HP");
            $sheet->setCellValue('K3', "Keterangan");
            $sheet->setCellValue('L3', "Jenis");
            $sheet->setCellValue('M3', "Nominal");
            $sheet->setCellValue('N3', "Keterangan barang");

            $j  = 4;
            $no = 1;
            if (count($transservicezis) == 0) {
                $lastno = 2;
                $lastj = 4;
            } else {
                $jenis = '';
                $category = '';
                foreach ($transservicezis as $key => $val) {
                    if ($val['service_zis_type'] == 1) {
                        $jenis = 'Instansi';
                    } else {
                        $jenis = 'Perorangan';
                    }

                    if ($val['service_zis_type'] == 1) {
                        $category = 'Zakat';
                    } elseif ($val['service_zis_type'] == 2) {
                        $category = 'Infaq';
                    } elseif ($val['service_zis_type'] == 3) {
                        $category = 'sedekah';
                    }


                    $sheet = $spreadsheet->getActiveSheet(0);
                    $spreadsheet->getActiveSheet()->setTitle("Rekap Zakat Infaq Sedekah");
                    $spreadsheet->getActiveSheet()->getStyle('B' . $j . ':N' . $j)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                    $sheet->setCellValue('B' . $j, $no);
                    $sheet->setCellValue('C' . $j, $jenis);
                    $sheet->setCellValue('D' . $j, $category);
                    $sheet->setCellValue('E' . $j, $val['service_zis_date']);
                    $sheet->setCellValue('F' . $j, $val['service_zis_name']);
                    $sheet->setCellValue('G' . $j, $this->getKelurahanName($val['kelurahan_id']));
                    $sheet->setCellValue('H' . $j, $this->getKecamatanName($val['kecamatan_id']));
                    $sheet->setCellValue('I' . $j, $val['service_zis_address']);
                    $sheet->setCellValue('J' . $j, $val['service_zis_phone']);
                    $sheet->setCellValue('K' . $j, $val['service_zis_remark']);

                    if ($val['service_zis_item_type'] == 1) {
                        $sheet->setCellValue('L' . $j, 'uang');
                    } else {
                        $sheet->setCellValue('L' . $j, 'barang');
                    }
                    $sheet->setCellValue('M' . $j, $val['service_zis_item_amount']);
                    $sheet->setCellValue('N' . $j, $val['service_zis_item_remark']);


                    $no++;
                    $j++;
                    $lastno = (int)$no;
                    $lastj = (int)$j;
                }

                // --------------------------------------TOTAL SUMARY----------------------------------------------//
                //------server-------//
                // $startRow = (int)$lastno - 1; 
                // $endRow = (int)$j - 1;

                //------local-------//
                $startRow = (int)$lastno - 2; 
                $endRow = (int)$j - 1;
                //dd($startRow,$endRow);
                $sheet = $spreadsheet->getActiveSheet(0);
                $spreadsheet->getActiveSheet()->getStyle('B' . $lastj . ':N' . $lastj)->getBorders()->getOutline()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                $sheet->setCellValue('H' . $lastj, 'Total Nominal:');
                $sumrangeQty = 'M' . $startRow . ':M' . $endRow;
                $sheet->setCellValue('M' . $lastj, '=SUM('. $sumrangeQty .')');
            }

            // ob_clean();
            $filename = 'Rekap Zakat infaq Sedekah ' . date('d M Y') . '.xls';
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');

            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xls');
            $writer->save('php://output');
        } else {
            echo "Maaf data yang di eksport tidak ada !";
        }
    }



        //export excel single
        public function exportSingle($service_zis_id)
        {
    
            Session::forget('datatransservicezisItem');
            Session::forget('data_transservicezis');
    
            if (!Session::get('start_date')) {
                $start_date     = date('Y-m-d');
            } else {
                $start_date = Session::get('start_date');
            }
    
            if (!Session::get('end_date')) {
                $end_date     = date('Y-m-d');
                $stop_date = date('Y-m-d', strtotime($end_date . ' +1 day'));
            } else {
                $end_date = Session::get('end_date');
                $stop_date = date('Y-m-d', strtotime($end_date . ' +1 day'));
            }
    
            $transservicezis = TransServiceZis::select('*')
                ->join('trans_service_zis_item', 'trans_service_zis_item.service_zis_id', 'trans_service_zis.service_zis_id')
                ->where('trans_service_zis.data_state', 0)
                ->where('trans_service_zis.created_at', '>=', $start_date)
                ->where('trans_service_zis.created_at', '<=', $stop_date)
                ->where('trans_service_zis.service_zis_id',$service_zis_id)
                ->get();
            // dd($transservicezis);
            $spreadsheet = new Spreadsheet();
    
            if (count($transservicezis) >= 0) {
                $spreadsheet->getProperties()->setCreator("SYSTEM INFORMATION")
                    ->setLastModifiedBy("SYSTEM INFORMATION")
                    ->setTitle("Rekap Zakat Infaq Sedekah")
                    ->setSubject("")
                    ->setDescription("Rekap Zakat Infaq Sedekah")
                    ->setKeywords("Rekap Zakat Infaq Sedekah")
                    ->setCategory("Rekap Zakat Infaq Sedekah");
    
                $sheet = $spreadsheet->getActiveSheet(0);
                $spreadsheet->getActiveSheet()->setTitle("Rekap Zakat Infaq Sedekah");
                $spreadsheet->getActiveSheet()->getPageSetup()->setFitToWidth(1);
                $spreadsheet->getActiveSheet()->getPageSetup()->setFitToWidth(1);
                $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(5);
                $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(20);
                $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(25);
                $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(20);
                $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(20);
                $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(10);
                $spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(25);
                $spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(20);
                $spreadsheet->getActiveSheet()->getColumnDimension('J')->setWidth(20);
                $spreadsheet->getActiveSheet()->getColumnDimension('K')->setWidth(20);
                $spreadsheet->getActiveSheet()->getColumnDimension('L')->setWidth(20);
                $spreadsheet->getActiveSheet()->getColumnDimension('M')->setWidth(20);
                $spreadsheet->getActiveSheet()->getColumnDimension('N')->setWidth(20);
    
    
    
    
                $spreadsheet->getActiveSheet()->mergeCells("B1:N1");
                $spreadsheet->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $spreadsheet->getActiveSheet()->getStyle('B1')->getFont()->setBold(true)->setSize(16);
    
                $spreadsheet->getActiveSheet()->getStyle('B3:N3')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                $spreadsheet->getActiveSheet()->getStyle('B3:N3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    
                $sheet->setCellValue('B1', "Zakat Infaq Sedekah Periode " . date('d M Y'));
                $sheet->setCellValue('B3', "No");
                $sheet->setCellValue('C3', "Jenis");
                $sheet->setCellValue('D3', "Kategori");
                $sheet->setCellValue('E3', "Tanggal");
                $sheet->setCellValue('F3', "Nama");
                $sheet->setCellValue('G3', "Kelurahan");
                $sheet->setCellValue('H3', "Kecamatan");
                $sheet->setCellValue('I3', "Alamat");
                $sheet->setCellValue('J3', "Nomor HP");
                $sheet->setCellValue('K3', "Keterangan");
                $sheet->setCellValue('L3', "Jenis");
                $sheet->setCellValue('M3', "Nominal");
                $sheet->setCellValue('N3', "Keterangan barang");
    
                $j  = 4;
                $no = 1;
                if (count($transservicezis) == 0) {
                    $lastno = 2;
                    $lastj = 4;
                } else {
                    $jenis = '';
                    $category = '';
                    foreach ($transservicezis as $key => $val) {
                        if ($val['service_zis_type'] == 1) {
                            $jenis = 'Instansi';
                        } else {
                            $jenis = 'Perorangan';
                        }
    
                        if ($val['service_zis_type'] == 1) {
                            $category = 'Zakat';
                        } elseif ($val['service_zis_type'] == 2) {
                            $category = 'Infaq';
                        } elseif ($val['service_zis_type'] == 3) {
                            $category = 'sedekah';
                        }
    
    
                        $sheet = $spreadsheet->getActiveSheet(0);
                        $spreadsheet->getActiveSheet()->setTitle("Rekap Zakat Infaq Sedekah");
                        $spreadsheet->getActiveSheet()->getStyle('B' . $j . ':N' . $j)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                        $sheet->setCellValue('B' . $j, $no);
                        $sheet->setCellValue('C' . $j, $jenis);
                        $sheet->setCellValue('D' . $j, $category);
                        $sheet->setCellValue('E' . $j, $val['service_zis_date']);
                        $sheet->setCellValue('F' . $j, $val['service_zis_name']);
                        $sheet->setCellValue('G' . $j, $this->getKelurahanName($val['kelurahan_id']));
                        $sheet->setCellValue('H' . $j, $this->getKecamatanName($val['kecamatan_id']));
                        $sheet->setCellValue('I' . $j, $val['service_zis_address']);
                        $sheet->setCellValue('J' . $j, $val['service_zis_phone']);
                        $sheet->setCellValue('K' . $j, $val['service_zis_remark']);
    
                        if ($val['service_zis_item_type'] == 1) {
                            $sheet->setCellValue('L' . $j, 'uang');
                        } else {
                            $sheet->setCellValue('L' . $j, 'barang');
                        }
                        $sheet->setCellValue('M' . $j, $val['service_zis_item_amount']);
                        $sheet->setCellValue('N' . $j, $val['service_zis_item_remark']);
    
    
                        $no++;
                        $j++;
                        $lastno = (int)$no;
                        $lastj = (int)$j;
                    }
                }
    
                // ob_clean();
                $filename = 'Rekap Zakat infaq Sedekah ' . date('d M Y') . '.xls';
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment;filename="' . $filename . '"');
                header('Cache-Control: max-age=0');
    
                $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xls');
                $writer->save('php://output');
            } else {
                echo "Maaf data yang di eksport tidak ada !";
            }
        }




    public function getCorekelurahan(Request $request)
    {
        $kecamatan_id   = $request->kecamatan_id;
        $data = '';


        $kelurahan = CoreKelurahan::where('kecamatan_id', $kecamatan_id)
            ->where('data_state', 0)
            ->get();

        $data .= "<option value=''>--Choose One--</option>";
        //     $data .= "<option value='0'>".$item['item_name']."</option>\n";
        foreach ($kelurahan as $mp) {
            $data .= "<option value='$mp[kelurahan_id]'>$mp[kelurahan_name]</option>\n";
        }

        return $data;
    }

    public function getKelurahanName($kelurahan_id)
    {
        $kelurahan = CoreKelurahan::where('kelurahan_id', $kelurahan_id)
            ->first();
        return $kelurahan['kelurahan_name'] ?? '';
    }

    public function getKecamatanName($kecamatan_id)
    {
        $kecamatan = CoreKecamatan::where('kecamatan_id', $kecamatan_id)
            ->first();
        return $kecamatan['kecamatan_name'] ?? '';
    }
}
