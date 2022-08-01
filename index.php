if(mysql_num_rows($VFODetails) > 0){
    $html5 .="<h5 style='font-size:11pt;' >{$ClientInfo['company_name']} (VFO)</h5>";
    $html5 .='<table border="1" width="600" cellpadding="2" cellspacing="2" style="font-size:11pt;" >';
    $html5 .="<tr>";
    $html5 .="<th style='font-size:15pt;background-color:#607d8b;color:#fff;font-weight:bold;'>Date</th>";
    $html5 .="<th style='font-size:15pt;background-color:#607d8b;color:#fff;font-weight:bold;'>Time</th>";
    $html5 .="<th style='font-size:15pt;background-color:#607d8b;color:#fff;font-weight:bold;'>Call From</th>";
    $html5 .="<th style='font-size:15pt;background-color:#607d8b;color:#fff;font-weight:bold;'>Pulse</th>";
    $html5 .="<th style='font-size:15pt;background-color:#607d8b;color:#fff;font-weight:bold;'>Rate</th>";
    $html5 .="</tr>";
    $VFOTotal = 0;
    while($inb = mysql_fetch_assoc($VFODetails)){
        
        
        $start_date1 = $start_date;
        $call_date = strtotime(date('Y-m-d',strtotime($inb['CallDate'])));
        foreach($period_arr as $end_date)
        {    
            if($call_date>=strtotime($start_date1) && $call_date<strtotime($end_date))
            {
                $data[$end_date]['VFOCallCharge'] += round($inb['Unit']*$PlanDetails['VFOCallCharge'],2);
                break;
            }
            else
            {
                $start_date1 =   $end_date; 
            }
            $Vfonew_cycle_start = $start_date1;
            $Vfonew_cycle_end = $end_date;
        }
        $inb['amount'] = round($inb['Unit']*$PlanDetails['VFOCallCharge'],2);;
        $VfoData[$inb['CallDate']][] = $inb;
        
    }
    
    foreach($VfoData as $call_date=>$inb_arr)
    {
        $call_date = substr($call_date,0,10);
        foreach($inb_arr as $inb)
        {
            if(strtotime($call_date)>=strtotime($Vfonew_cycle_start) && strtotime($call_date)>=strtotime($FromDate))
            {
                $html5 .="<tr>";
                $html5 .="<td>".$inb['CallDate1']."</td>";
                $html5 .="<td>".$inb['CallTime']."</td>";
                $html5 .="<td>".$inb['CallFrom']."</td>";
                $html5 .="<td>".$inb['Unit']."</td>";
                $html5 .="<td>".round($inb['Unit']*$PlanDetails['VFOCallCharge'],2)."</td>";
                $html5 .="</tr>";
                $VFOTotal += $inb['Unit'];
                $VFO['Unit'] += $inb['Unit'];
            }
        }    
    }
    
    

    $html5 .="<tr><td colspan='5' ><b>Total Vol {$VFOTotal}</b></td></tr>";
    $html5 .="</table>";
}



$fileName = "statement_".date('d_m_y_h_i_s');
header("Content-Type: application/vnd.ms-excel; name='excel'");
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=".$fileName.".xls");
header("Pragma: no-cache");
header("Expires: 0");

echo $html.$html1.$html2.$html3.$html4.$html5 ;die;
