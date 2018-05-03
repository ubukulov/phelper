<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use PHPExcel_IOFactory;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Border;
use PHPExcel_Style_NumberFormat;
use PHPExcel_Style_Fill;
use Illuminate\Support\Facades\DB;

class RectorateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       return view('rectorate/index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $id = (int) $id;
        switch ($id){
            case 1:
                $filename = base_path() . "/public/reports/rectorat/1.xls";
                $objReader = PHPExcel_IOFactory::createReader('Excel5');
                $objReader->setReadDataOnly(false);
                $objPHPExcel = $objReader->load($filename);
                $objPHPExcel->setActiveSheetIndex(0);
                $active_sheet = $objPHPExcel->getActiveSheet();

                $title = "Информация по обеспеченности студентов местами в общежитиях по состоянию на ".date("d.m.Y");
                $active_sheet->getStyle('D1')->getFont()->setBold(true);
                $active_sheet->setCellValue('D1',$title);

                // Кол-во общетижие
                $number_dorm = DB::select("SELECT COUNT(*) AS cnt  FROM nitro.dormitories");
                $active_sheet->setCellValue('F6',$number_dorm[0]->cnt);

                // Кол-во мест
                $count_place = DB::select("SELECT SUM(ND.beds) AS cnt_place FROM nitro.dormitories ND");
                $active_sheet->setCellValue('G6',$count_place[0]->cnt_place);

                // Кол-во иногородних студентов
                $nonresident = DB::select("SELECT COUNT(*) AS cnt FROM nitro.students NS WHERE NS.local=0 AND NS.isStudent=1");
                $active_sheet->setCellValue('H6',$nonresident[0]->cnt);
                # по гранту
                $non_resident_grant = DB::select("SELECT COUNT(*) AS cnt FROM nitro.students NS WHERE NS.local=0 AND NS.isStudent=1 AND NS.PaymentFormID<>1");
                $active_sheet->setCellValue('I6',$non_resident_grant[0]->cnt);
                # по платном отделении
                $non_resident_paid = DB::select("SELECT COUNT(*) AS cnt FROM nitro.students NS WHERE NS.local=0 AND NS.isStudent=1 AND NS.PaymentFormID=1");
                $active_sheet->setCellValue('J6',$non_resident_paid[0]->cnt);

                // Кол-во необеспеченных студентов
                $cnt_need_st1 = DB::select("SELECT COUNT(*) AS cnt FROM nitro.students NS WHERE NS.local=0 AND NS.isStudent=1 AND NS.dorm_state=2");
                $cnt_need_st2 = DB::select("SELECT COUNT(*) AS cnt FROM nitro.students NS WHERE NS.local=0 AND NS.isStudent=1 AND NS.dorm_state=2 AND NS.PaymentFormID<>1");
                $cnt_need_st3 = DB::select("SELECT COUNT(*) AS cnt FROM nitro.students NS WHERE NS.local=0 AND NS.isStudent=1 AND NS.dorm_state=2 AND NS.PaymentFormID=1");
                $active_sheet->setCellValue('K6',$cnt_need_st1[0]->cnt);
                $active_sheet->setCellValue('L6',$cnt_need_st2[0]->cnt);
                $active_sheet->setCellValue('M6',$cnt_need_st3[0]->cnt);

                // Кол-во проживающих студентов
                $cnt_sp_st1 = DB::select("SELECT COUNT(*) AS cnt FROM nitro.students NS WHERE NS.local=0 AND NS.isStudent=1 AND NS.dorm_state=3");
                $cnt_sp_st2 = DB::select("SELECT COUNT(*) AS cnt FROM nitro.students NS WHERE NS.local=0 AND NS.isStudent=1 AND NS.dorm_state=3 AND NS.PaymentFormID<>1");
                $cnt_sp_st3 = DB::select("SELECT COUNT(*) AS cnt FROM nitro.students NS WHERE NS.local=0 AND NS.isStudent=1 AND NS.dorm_state=3 AND NS.PaymentFormID=1");
                $active_sheet->setCellValue('N6',$cnt_sp_st1[0]->cnt);
                $active_sheet->setCellValue('O6',$cnt_sp_st2[0]->cnt);
                $active_sheet->setCellValue('P6',$cnt_sp_st3[0]->cnt);

                // Кол-во не нуждающихся студентов
                $cnt_no_st1 = DB::select("SELECT COUNT(*) AS cnt FROM nitro.students NS WHERE NS.local=0 AND NS.isStudent=1 AND NS.dorm_state=1");
                $cnt_no_st2 = DB::select("SELECT COUNT(*) AS cnt FROM nitro.students NS WHERE NS.local=0 AND NS.isStudent=1 AND NS.dorm_state=1 AND NS.PaymentFormID<>1");
                $cnt_no_st3 = DB::select("SELECT COUNT(*) AS cnt FROM nitro.students NS WHERE NS.local=0 AND NS.isStudent=1 AND NS.dorm_state=1 AND NS.PaymentFormID=1");
                $active_sheet->setCellValue('Q6',$cnt_no_st1[0]->cnt);
                $active_sheet->setCellValue('R6',$cnt_no_st2[0]->cnt);
                $active_sheet->setCellValue('S6',$cnt_no_st3[0]->cnt);

                $procent_need = round((($cnt_sp_st1[0]->cnt * 100) / $cnt_need_st1[0]->cnt));
                $active_sheet->setCellValue('N16',date("d.m.Y"));

                $active_sheet->setCellValue('T6',$procent_need);

                header("Content-Type:application/vnd.ms-excel");
                header("Content-Disposition:attachment;filename=" .$title . ".xls");
                $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
                $objWriter->save("php://output");
                exit();
                break;
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
