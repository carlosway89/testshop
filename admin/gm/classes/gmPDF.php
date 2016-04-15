<?php
	
	/* 
	--------------------------------------------------------------
	gmPDF.php  2014-06-21 gm
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]

   IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE. 
   MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES, USE THE
   NEW GX-ENGINE LIBRARIES INSTEAD.
	--------------------------------------------------------------
	*/

	MainFactory::load_class('TCPDF');


	/*
	*	class to create pdfs, using fpdf
	*/
	class gmPDF_ORIGIN extends TCPDF {

		/*
		*	standard layout
		*/
		var $pdf_orientation = 'P'; 

		var $pdf_unit = 'mm';

		var $pdf_format = 'A4';			
		
		var $pdf_cell_height;

		/*
		*	set diplay layout in the pdf reader
		*/
		var $pdf_display_zoom;

		var $pdf_display_layout;

		/*
		*	margins
		*/
		var $pdf_top_margin;

		var $pdf_left_margin;

		var $pdf_right_margin;

		var $pdf_bottom_margin;

		/*
		*	use following features 
		*/
		var $pdf_fix_header;

		var $pdf_use_header;

		var $pdf_use_footer;

		/*
		*	values generated in class
		*/
		var $pdf_inner_width;

		var $pdf_page_break;

		var $pdf_footer_position;

		/*
		*	pdf protection values
		*/		
		var $pdf_protection = array();	
		
		var $encrypted;          //whether document is protected

		var $Uvalue;             //U entry in pdf document

		var $Ovalue;             //O entry in pdf document

		var $Pvalue;             //P entry in pdf document

		var $enc_obj_id;         //encryption object id

		var $last_rc4_key;       //last RC4 key encrypted (cached for optimisation)

		var $last_rc4_key_c;     //last RC4 computed key

		/*
		*	class constructor
		*/
		function __construct($gm_pdf_values) {
			
			// -> to call the parent class constructor, load defaults
			parent::__construct($this->pdf_orientation, $this->pdf_unit, $this->pdf_format, true, 'UTF-8');
		
			// -> use protection
			if($gm_pdf_values['GM_PDF_USE_PROTECTION']) {
				$this->encrypted=false;
				$this->last_rc4_key='';
				$this->padding="\x28\xBF\x4E\x5E\x4E\x75\x8A\x41\x64\x00\x4E\x56\xFF\xFA\x01\x08".
								"\x2E\x2E\x00\xB6\xD0\x68\x3E\x80\x2F\x0C\xA9\xFE\x64\x53\x69\x7A";
			}

			// -> set default values 
			$this->pdf_top_margin		= $gm_pdf_values['GM_PDF_TOP_MARGIN'];
			$this->pdf_left_margin		= $gm_pdf_values['GM_PDF_LEFT_MARGIN'];
			$this->pdf_right_margin		= $gm_pdf_values['GM_PDF_RIGHT_MARGIN'];
			$this->pdf_bottom_margin	= $gm_pdf_values['GM_PDF_BOTTOM_MARGIN'];
			$this->pdf_fix_header		= $gm_pdf_values['GM_PDF_FIX_HEADER'];
			$this->pdf_use_header		= $gm_pdf_values['GM_PDF_USE_HEADER'];
			$this->pdf_use_footer		= $gm_pdf_values['GM_PDF_USE_FOOTER'];
			$this->pdf_display_zoom		= $gm_pdf_values['GM_PDF_DISPLAY_ZOOM'];
			$this->pdf_display_layout	= $gm_pdf_values['GM_PDF_DISPLAY_LAYOUT'];
			$this->pdf_cell_height		= $gm_pdf_values['GM_PDF_CELL_HEIGHT'];			
			
			// -> to make the function Footer() work properly
//			parent::AliasNbPages();
			
			// -> set margins (left, top, right)
			parent::SetMargins($this->pdf_left_margin, $this->pdf_top_margin, $this->pdf_right_margin); 			
			
			// -> to set the default font/style/size
			$this->getFont($this->pdf_fonts['FOOTER']);
		
			// -> set the displaymode of the pdfument (fullpage, fullwidth, real, default + single, continuous, two)
			parent::SetDisplayMode($this->pdf_display_zoom, $this->pdf_display_layout);
			
			// -> width to use
			$this->pdf_inner_width = $this->w - $this->pdf_left_margin - $this->pdf_right_margin;			

			// -> get page break
			$this->pdf_page_break = $this->h - $this->GetAutoPageBreak();
			
			// -> get footer pos
			$this->pdf_footer_position = $this->GetAutoPageBreak();

			// -> to set the page break, auto, 
			parent::SetAutoPageBreak(true, $this->pdf_page_break);
			
			return;
		}
		

		/* 
		*	-> define PDF Header 
		*/
		function Header() {

			// -> to check if header should be fixed on every page, do not show header on attachments
			if($this->pdf_fix_header == 1 && $this->pdf_use_header == 1 && empty($this->pdf_is_attachment)) {	
				// -> call function of daughter class
				$this->getHeader();
			} elseif(!empty($this->pdf_is_attachment)) {
				$this->getCondralHeader();
			}			

			return;
		}


		/* 
		*	-> body is defined in daughter class
		*/
		/*
		function Body() {

			return;
		}
		*/


		/* 
		*	-> define PDF Footer 
		*/
		function Footer() {
			
			// -> check if footer wants to be used
			if($this->pdf_use_footer == 1) {
				// -> call function of daughter class
				$this->getFooter();
			}
			return;
		}


		/* 
		*	-> count cell heigt 
		*/
		function countMaxHeight($string_width, $cell_width) {
			
			if(!empty($cell_width)) {
				$erg = $string_width / $cell_width;
				if($erg < 1) {
					return ceil($erg);	
				} else {
					return floor($erg);
				}
			}
		}


		/* 
		*	-> get position of the page break in relation of the footer
		*/
		function GetAutoPageBreak() {

			if($this->pdf_use_footer == '1') {
				
				$count_max = 0;
				for($i =0; $i < count($this->pdf_footer); $i++) {

					$count = 0;
					$lines = explode("\n", $this->pdf_footer[$i]);
					
					foreach($lines as $line) {
						
						$string_width = parent::GetStringWidth($line);
						
						if($string_width + 1 > ($this->pdf_inner_width / count($this->pdf_footer))) {
							$count = $count + $this->countMaxHeight($string_width, $this->pdf_inner_width / count($this->pdf_footer));					
						}
						if($string_width != 0) {
							$count++;
						}
					}
					
					if($count_max < $count) {
						$count_max = $count;
					}
				}

				return($this->h - $this->pdf_bottom_margin - ($count_max * $this->pdf_cell_height));

			} else {

				return($this->h - $this->pdf_bottom_margin);
			}
			
		}	


		/* 
		*	-> get rgb out of hex
		*/
		function getRGB($hex) {
			
			$hex_array = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9,
				'A' => 10, 'B' => 11, 'C' => 12, 'D' => 13, 'E' => 14,
				'F' => 15);
			$hex = str_replace('#', '', strtoupper($hex));
			if (($length = strlen($hex)) == 3) {
				$hex = $hex{0}.$hex{0}.$hex{1}.$hex{1}.$hex{2}.$hex{2};
				$length = 6;
			}
			if ($length != 6 or strlen(str_replace(array_keys($hex_array), '', $hex)))
				return NULL;
			$rgb['r'] = $hex_array[$hex{0}] * 16 + $hex_array[$hex{1}];
			$rgb['g'] = $hex_array[$hex{2}] * 16 + $hex_array[$hex{3}];
			$rgb['b']= $hex_array[$hex{4}] * 16 + $hex_array[$hex{5}];
			return $rgb;
		}


		/* 
		*	-> get Fonts and Drawcolor
		*/
		function getFont($font, $style='') {

			$rgb = $this->getRGB($font[3]);	
			
			parent::SetTextColor((int)$rgb['r'], (int)$rgb['g'], (int)$rgb['b']); 
			
			if(!empty($style)) {				
				parent::SetFont($font[0], $style, (int)$font[2]);
			} else {
				parent::SetFont($font[0], $font[1], (int)$font[2]);
			}
		}


		/* 
		*	-> get cell height 
		*/
		function getCellHeight() {
			
			return $this->pdf_cell_height;
		}


		/* 
		*	-> get left margin 
		*/
		function getLeftMargin() {
			
			return $this->pdf_left_margin;
		}


		/* 
		*	-> get top margin 
		*/
		function getTopMargin() {
			
			return $this->pdf_top_margin;
		}


		/* 
		*	-> get inner width 
		*/
		function getInnerWidth() {
			
			return $this->pdf_inner_width;
		}


		/* 
		*	-> get position of the footer 
		*/
		function getFooterPos() {
			
			return $this->pdf_footer_position;
		}


		/* 
		*	-> get page break
		*/
		function getPageBreak() {
			
			return $this->pdf_page_break;
		}


		/****************************************************************************
		* Software: FPDF_Protection                                                 *
		* Version:  1.02                                                            *
		* Date:     2005/05/08                                                      *
		* Author:   Klemen VODOPIVEC                                                *
		* License:  Freeware                                                        *
		*                                                                           *
		* You may use and modify this software as you wish as stated in original    *
		* FPDF package.                                                             *
		*                                                                           *
		* Thanks: Cpdf (http://www.ros.co.nz/pdf) was my working sample of how to   *
		* implement protection in pdf.                                              *
		****************************************************************************/

		/****************************************************************************
		*                                                                           
		* Function to set permissions as well as user and owner passwords
		*
		* - permissions is an array with values taken from the following list:
		*   copy, print, modify, annot-forms
		*   If a value is present it means that the permission is granted
		* - If a user password is set, user will be prompted before document is opened
		* - If an owner password is set, document can be opened in privilege mode with no
		*   restriction if that password is entered
		*/
		function SetProtection($permissions=array(),$user_pass='',$owner_pass=null) {
			
			$options = array('print' => 4, 'modify' => 8, 'copy' => 16, 'annot-forms' => 32 );
			$protection = 192;
			
			foreach($permissions as $permission) {
				if(!isset($options[$permission])) {
					$this->Error('Incorrect permission: '.$permission);
				}
				$protection += $options[$permission];
			}
			
			if ($owner_pass === null)
				
			$owner_pass = uniqid(rand());
			$this->encrypted = true;
			$this->_generateencryptionkey($user_pass, $owner_pass, $protection);
		}

		/****************************************************************************
		*                                                                           *
		*                              Private methods                              *
		*                                                                           *
		****************************************************************************/

		function _putstream($s) {
			if ($this->encrypted) {
				$s = $this->_RC4($this->_objectkey($this->n), $s);
			}
			parent::_putstream($s);
		}

		function _textstring($s) {
			if ($this->encrypted) {
				$s = $this->_RC4($this->_objectkey($this->n), $s);
			}
			return parent::_textstring($s);
		}

		/**
		* Compute key depending on object number where the encrypted data is stored
		*/
		function _objectkey($n) {
			return substr($this->_md5_16($this->encryption_key.pack('VXxx',$n)),0,10);
		}

		/**
		* Escape special characters
		*/
		function _escape($s) {
			$s=str_replace('\\','\\\\',$s);
			$s=str_replace(')','\\)',$s);
			$s=str_replace('(','\\(',$s);
			$s=str_replace("\r",'\\r',$s);
			return $s;
		}

		function _putresources() {
			parent::_putresources();
			if ($this->encrypted) {
				$this->_newobj();
				$this->enc_obj_id = $this->n;
				$this->_out('<<');
				$this->_putencryption();
				$this->_out('>>');
				$this->_out('endobj');
			}
		}

		function _putencryption() {
			$this->_out('/Filter /Standard');
			$this->_out('/V 1');
			$this->_out('/R 2');
			$this->_out('/O ('.$this->_escape($this->Ovalue).')');
			$this->_out('/U ('.$this->_escape($this->Uvalue).')');
			$this->_out('/P '.$this->Pvalue);
		}

		function _puttrailer() {
			parent::_puttrailer();
			if ($this->encrypted) {
				$this->_out('/Encrypt '.$this->enc_obj_id.' 0 R');
				$this->_out('/ID [()()]');
			}
		}

		/**
		* RC4 is the standard encryption algorithm used in PDF format
		*/
		function _RC4($key, $text) {
			if ($this->last_rc4_key != $key) {
				$k = str_repeat($key, 256/strlen($key)+1);
				$rc4 = range(0,255);
				$j = 0;
				for ($i=0; $i<256; $i++){
					$t = $rc4[$i];
					$j = ($j + $t + ord($k{$i})) % 256;
					$rc4[$i] = $rc4[$j];
					$rc4[$j] = $t;
				}
				$this->last_rc4_key = $key;
				$this->last_rc4_key_c = $rc4;
			} else {
				$rc4 = $this->last_rc4_key_c;
			}

			$len = strlen($text);
			$a = 0;
			$b = 0;
			$out = '';
			for ($i=0; $i<$len; $i++){
				$a = ($a+1)%256;
				$t= $rc4[$a];
				$b = ($b+$t)%256;
				$rc4[$a] = $rc4[$b];
				$rc4[$b] = $t;
				$k = $rc4[($rc4[$a]+$rc4[$b])%256];
				$out.=chr(ord($text{$i}) ^ $k);
			}

			return $out;
		}

		/**
		* Get MD5 as binary string
		*/
		function _md5_16($string) {
			return pack('H*',md5($string));
		}

		/**
		* Compute O value
		*/
		function _Ovalue($user_pass, $owner_pass) {
			$tmp = $this->_md5_16($owner_pass);
			$owner_RC4_key = substr($tmp,0,5);
			return $this->_RC4($owner_RC4_key, $user_pass);
		}

		/**
		* Compute U value
		*/
		function _Uvalue() {
			return $this->_RC4($this->encryption_key, $this->padding);
		}

		/**
		* Compute encryption key
		*/
		function _generateencryptionkey($user_pass, $owner_pass, $protection) {
			// Pad passwords
			$user_pass = substr($user_pass.$this->padding,0,32);
			$owner_pass = substr($owner_pass.$this->padding,0,32);
			// Compute O value
			$this->Ovalue = $this->_Ovalue($user_pass,$owner_pass);
			// Compute encyption key
			$tmp = $this->_md5_16($user_pass.$this->Ovalue.chr($protection)."\xFF\xFF\xFF");
			$this->encryption_key = substr($tmp,0,5);
			// Compute U value
			$this->Uvalue = $this->_Uvalue();
			// Compute P value
			$this->Pvalue = -(($protection^255)+1);
		}
	}

MainFactory::load_origin_class('gmPDF');
