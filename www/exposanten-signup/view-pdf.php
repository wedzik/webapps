<?php
session_start();
require_once dirname(__FILE__)."/config.php";
include_once INCLUDE_SCRIPTS_PATCH . "manager.php";
require_once INCLUDE_LIBS_PATCH . "Barcode39/Barcode39.php";
require_once INCLUDE_LIBS_PATCH . "MPDF57/mpdf.php";
$manager->processAdminPageLogIn();
if(!$manager->haveAcess){
    header("Location: http://".$_SERVER['HTTP_HOST'].MAIN_ROOT_DIR."/admin.php");
}

class pdfIvoice {
    public $pdf;
    public $id;
    public $PhotoPatch;

    private $_date;
    private $_order_number;

    private $_item;
    private $_total;
    private $_company;
    private $_name;
    private $_address;
    private $_phone;
    private $_email;

    public  $btw_value=0;
    private $_moneyAmount = 0;
    private $_moneyBTWAmount = 0;

    private $companyName = '';
    private $defLeftSpace = 20;

    public function pdfOut(){
        $bc = new Barcode39($this->_order_number);
        $bc->barcode_text = false;
        $bc->barcode_width = 280;
        $bc->barcode_height = 70;
        $barcodeFileNAme = "./bc.gif";
        $bc->draw($barcodeFileNAme);

        ob_start();
    ?>
<style>
    body{
        font-family: Arial, Helvetica, Sans-Serif;
        font-size: 14px;
    }

    table  { border-spacing: 0; }
    td { padding: 0; }

    .header-info-table{
        display: block;
        float: left;
        padding-top: 0px;
        padding-left: 25px;
        width: 100%;
    }
    .header-info-table td {
        padding: 0px 0px;
        height: 14px;
    }

    .header-table{
        display: block;
        padding-top: 0px;
        text-align: left;
        width: 100%;
    }

    .header-table td {
        padding: 0px 0px;
    }

    .factuur_header{
        font-size: 35px;
        color: gray;
        text-align: left;
        display: block;
        font-weight: bold;

    }

    .gray-text{
        color: gray;
    }

    .order-items{
        display: block;
        padding-top: 10px;
        padding-left: 25px;
        width: 100%;
        text-align: right;
    }

    .order-items th{
        color: gray;
        font-weight: normal;
    }

    .order-items th,
    .order-items td{
        border: 1px solid black;
        border-right-width: 0px;
        padding: 2px 5px;
    }
    .order-items .last-cell{
        border-right-width: 1px;
    }

    .order-items .item-row td{
        border-top: 0px;
        border-bottom: 0px;
    }

    .order-items .item-row-last td{
        border-top: 0px;
        font-size: 12px;
        font-weight: bold;
    }

    .order-items .item-row-summary td{
        border-top: 0px;
        font-weight: bold;
    }

    .order-items .item-row-summary-2 td{
        border: 0px;
    }
</style>
<table class="header-info-table" cellspacing="0" border="0" border="0">
    <tr>
        <td colspan="3">
            <?php echo ORDER_HEADER; ?></b>
        </td>
    </tr>
    <tr>
        <td rowspan="2" style="width: 50%">
            <b> <?php echo $this->_company. "<br/>".$this->_name . "<br/>".$this->_address; ?></b>
        </td>
        <td  style="width: 25%; text-align: right;" class="gray-text"></td>
        <td style="width: 25%"><b></b></td>
    </tr>
    <tr>
        <td style="text-align: right;"  class="gray-text"></td>
        <td><b></b></td>
    </tr>
</table>

<table class="header-table" cellspacing="0" border="0" >
    <tr>
        <td colspan="3"  style="width: 28%; padding-top: 0px; padding-bottom: 25px;" >
            <div class="factuur_header"><img src="<?php echo "./bc.gif"?>" style="padding-left: 25px;"></div>
        </td>
    </tr>
    <tr>
        <td rowspan="3" style="padding-top: 0px; width: 40% " ><div class="factuur_header">&nbsp;&nbsp;Transactie</div></td>
        <td style="text-align: right;" class="gray-text"><?php echo $this->_date; ?></td>
        <td style="text-align: left; padding-left: 8px;"><b>Datum</b></td>
    </tr>
    <tr>
        <td style="text-align: right;"  class="gray-text"><?php echo $this->_order_number; ?></td>
        <td style="text-align: left; padding-left: 8px;"><b>Factuur Nummer</b></td>
    </tr>
    <tr>
        <td style="text-align: right;"  class="gray-text"><?php echo $this->_email; ?></td>
        <td style=" padding-left: 8px;">Email Boekhouding</td>
    </tr>
</table>

<table class="order-items"  cellspacing="0" border="0" cellpadding="0">
    <tr>
        <th  style="width: 65%; text-align: left;">Omschrijving</th>
        <th  style="width: 10%;">Aantal</th>
        <th style="width: 12%;">Prijs</th>
        <th style="width: 13%;" class="last-cell">Bedrag</th>
    </tr>
                <tr class="item-row">
                    <td style="text-align: left;"><?php echo $this->_item; ?></td>
                    <td><?php echo number_format(1,2); ?></td>
                    <td><?php echo number_format($this->_moneyAmount,2); ?></td>
                    <td class="last-cell"><?php echo number_format($this->_moneyAmount,2); ?></td>
                </tr>
    <tr class="item-row">
        <td><br/></td> <td></td> <td></td> <td class="last-cell"></td>
    </tr>
    <tr class="item-row">
        <td style="text-align: left;">>>> SUBTOTAAL</td>
        <td></td>
        <td></td>
        <td class="last-cell"><?php echo number_format($this->_moneyAmount,2); ?></td>
    </tr>
    <tr class="item-row">
        <td style="text-align: left;">>>> BTW&nbsp;<?php echo ($this->btw_value * 100) . "%"; ?></td>
        <td></td>
        <td><?php echo number_format(($this->btw_value* 100), 2) . "%"; ?></td>
        <td class="last-cell"><?php echo number_format($this->_moneyBTWAmount, 2); ?></td>
    </tr>

    <tr class="item-row-last">
        <td style="text-align: left; padding-left: 15px; padding-top: 300px;">&nbsp;</td>
        <td></td>
        <td></td>
        <td class="last-cell"></td>
    </tr>

    <tr class="item-row-summary">
        <td colspan="2" rowspan="2" style="font-weight:normal;  padding-left: 5px; text-align: left; border: 0px; font-size: 12px;">
            <?php echo ORDER_FOOTER; ?>
        </td>
        <td>Totaal</td>
        <td class="last-cell gray-text">&euro;<?php echo number_format($this->_total, 2); ?></td>
    </tr>
    <tr class="item-row-summary-2">
        <td><br/></td>
        <td class="last-cell"></td>
    </tr>
</table>
<?php
        $html_content = ob_get_clean();
        $this->pdf = new mPDF('', '', 0, '', 20, 20, 10, 20, 9, 9, 'L');//('c','A4','','',32,25,27,25,16,13);
        /*$this->pdf->SetImportUse();
        $this->pdf->SetSourceFile($_SERVER['DOCUMENT_ROOT'].'/_html/images/watermark.pdf');
        $tplId = $this->pdf->ImportPage(1);
        $this->pdf->UseTemplate($tplId);*/
        $this->pdf->WriteHTML($html_content);
        unlink($barcodeFileNAme);
    }

    public function printPdf($member, $out_to_browser = false ){
        $this->_order_number = $member->id;
        $this->_date = date('d-m-Y', $member->time);
        $this->_company = $member->company_names;
        $this->_name = $member->contact_name;
        $this->_address = $member->address;
        $this->_email = $member->email;
        $this->_phone = $member->phone;

        $this->_item = $member->when."<br/>".$member->what;
        $this->_item = str_replace("|" ,"<br/>",$this->_item );

        $when_vals = explode("|", $member->when);
        $when_count = 0;
        foreach($when_vals as  $when_val){
            if(strlen(trim($when_val)) > 0) {
                $when_count++;
            }
        }

        $what_vals = explode("|", $member->what);
        $what_sum = " ";
        try {
            foreach($what_vals as  $what_val){
                if(strlen(trim($what_val)) > 0) {
                    $euro_pos = strpos($what_val, EURO_PARSE_KEY);
                    $tmp = substr($what_val, $euro_pos+4);
                    $tmp = substr($tmp, 0, strpos($tmp," "));
                    $money_val = floatval($tmp);
                    $what_sum += $money_val;
                }
            }
        } catch (Exception $e) {
            $what_sum = 0;
        }

        $this->_item .= " ".$when_count." ".$what_sum;
        $this->_moneyAmount = $when_count * $what_sum;
        $this->_moneyBTWAmount = $this->_moneyAmount*BTW_VALUE;
        $this->btw_value = BTW_VALUE;
        $this->_total = $this->_moneyAmount+$this->_moneyBTWAmount;
        $fileName = "ORDER_$member->id.pdf";

        $this->pdfOut();
        if ($out_to_browser)
            $this->pdf->Output($fileName, 'I');
        else {
            if (!is_dir(ORDERS_PATCH)) { mkdir(ORDERS_PATCH); }
            $this->pdf->Output(ORDERS_PATCH . $fileName, 'F');
        }
        return $fileName;
    }
}

//$manager->printPdfInvoice($event_name, $id)
if (isset($_GET["eid"]) && isset($_GET["uid"])&& isset($_GET["fid"])) {
    $file_sufix = str_replace($_GET['eid']."_","",$_GET['fid']);
    $member = $manager->getMemberByEventAndId($_GET["eid"], $_GET["uid"], $file_sufix);
    //$member = $manager->getMemberByEventAndId("test1", "y5PKEDDiCXzR");
    if ($member !== false) {
        $invoice = new pdfIvoice();
        $invoice->printPdf($member, true);
    }
}
?>