<?php
/**
 * ETcPdf class file.
 *
 * @author MetaYii
 * @link http://www.yiiframework.com/
 * @copyright Copyright &copy; 2008 MetaYii
 * @license
 *
 *  Copyright (C) 2002-2008 MetaYii.
 *
 * 	This program is free software: you can redistribute it and/or modify
 * 	it under the terms of the GNU Lesser General Public License as published by
 * 	the Free Software Foundation, either version 3.0 of the License, or
 * 	(at your option) any later version.
 *
 * 	This program is distributed in the hope that it will be useful,
 * 	but WITHOUT ANY WARRANTY; without even the implied warranty of
 * 	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * 	GNU Lesser General Public License for more details.
 *
 * 	You should have received a copy of the GNU Lesser General Public License
 * 	along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * 	See the lgpl-3.0.txt file for more information.
 *
 * For third party licences and copyrights, please see:
 *
 * @see tcpdf/LICENSE.TXT
 * @see http://partners.adobe.com/public/developer/support/topic_legal_notices.html
 *
 */

/**
 * Include the the TCPDF class. IMPORTANT: Don't forget to customize its configuration
 * if needed.
 */
require_once(dirname(__FILE__).'/tcpdf/tcpdf.php');

/**
 * ETcPdf is a simple wrapper for the TCPDF library.
 * @see http://www.tecnick.com/public/code/cp_dpage.php?aiocp_dp=tcpdf
 *
 * @author MetaYii
 * @package application.extensions.tcpdf
 * @since 1.1
 */

class MYPDF extends TCPDF {

	//Page header
	public function Header() {
		// Logo
		$image_file = K_PATH_IMAGES.'logo_example.jpg';
		$this->Image($image_file, 10, 10, 15, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
		// Set font
		$this->SetFont('helvetica', 'B', 20);
		// Title
		$this->Cell(0, 15, '<< TCPDF Example 003 >>', 0, false, 'C', 0, '', 0, false, 'M', 'M');
	}

	// Page footer
	public function Footer() {
		// Position at 15 mm from bottom
		$this->SetY(-15);
		// Set font
		$this->SetFont('helvetica', 'I', 8);
		// Page number
		$this->Cell(0, 10, $this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
	}
}


class ETcPdf
{
   /**
    * The internal TCPDF object.
    *
    * @var object TCPDF
    */
   public  $myTCPDF = null;

  	/**
	 * @param $orientacion: 0:Portlain, 1:Landscape
	 * @param $unit: /cm/mm/
	 * @param $format: /A3/A4/A5/Letter/Legal/array(w,h)
	 */
   public function __construct($orientation, $unit, $format, $unicode, $encoding)
   {
      if ($orientation != 'P' && $orientation != 'L')
         throw new CException(Yii::t('ETcPdf', 'The orientation must be "P" or "L"'));

      if (!in_array($unit, array('pt', 'mm', 'cm', 'in')))
         throw new CException(Yii::t('ETcPdf', 'The unit must be "pt", "in", "cm" or "mm"'));

      if (!is_string($format) && !is_array($format))
         throw new CException(Yii::t('ETcPdf', 'The format must be string or array.'));
      if (is_string($format)) {
         if (!in_array($format, array('A3', 'A4', 'A5', 'Letter', 'Legal')))
            throw new CException(Yii::t('ETcPdf', 'The format must be one of A3, A4, A5, Letter or Legal'));
      }
      else {
         if (!is_numeric($format[0]) && !is_numeric($format[1]))
            throw new CException(Yii::t('ETcPdf', 'The format must be array(w, h)'));
      }

      if (!is_bool($unicode))
         throw new CException(Yii::t('ETcPdf', '"unicode" must be a boolean value'));

		$this->myTCPDF = //new TCPDF($orientation, $unit, $format, $unicode, $encoding);
                 new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
   	//define ("K_PATH_CACHE", Yii::app()->getRuntimePath());
   }
   
   /**
    * PHP defined magic method
    *
    */
	public function __call($method, $params)
	{
		if (is_object($this->myTCPDF) && get_class($this->myTCPDF)==='MYPDF') return call_user_func_array(array($this->myTCPDF, $method), $params);
		else throw new CException(Yii::t('ETcPdf', 'Can not call a method of a non existent object'));
	}

	public function __set($name, $value)
	{
	   if (is_object($this->myTCPDF) && get_class($this->myTCPDF)==='MYPDF') $this->myTCPDF->$name = $value;
	   else throw new CException(Yii::t('ETcPdf', 'Can not set a property of a non existent object'));
	}

	public function __get($name)
	{
	   if (is_object($this->myTCPDF) && get_class($this->myTCPDF)==='MYPDF') return $this->myTCPDF->$name;
	   else throw new CException(Yii::t('ETcPdf', 'Can not access a property of a non existent object'));
	}

	/**
	 * Cleanup work before serializing.
	 * This is a PHP defined magic method.
	 * @return array the names of instance-variables to serialize.
	 */
	public function __sleep()
	{
	}

	/**
	 * This method will be automatically called when unserialization happens.
	 * This is a PHP defined magic method.
	 */
	public function __wakeup()
	{
	}
}