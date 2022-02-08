<?php
/*
 *   (c) Semen Alekseev
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */

class Id3v2 {
	public $error;
	
	private $tags = array(
		'TALB' => 'Album',
		'TCON' => 'Genre',
		'TENC' => 'Encoder',
		'TIT2' => 'Title',
		'TPE1' => 'Artist',
		'TPE2' => 'Ensemble',
		'TYER' => 'Year',
		'TCOM' => 'Composer',
		'TCOP' => 'Copyright',
		'TRCK' => 'Track',
		'WXXX' => 'URL',
		'COMM' => 'Comment'
		);
		 
	private function decTag($tag, $type){
		//TODO- handling of comments is quite weird
		//but I don't know how it is encoded so I will leave the way it is for now
		if ($type == 'COMM')
		{
			$tag = substr($tag, 0, 3) . substr($tag, 10);
		}
		//mb_convert_encoding is corrupted in some versions of PHP so I use iconv
		switch (ord($tag[2]))
		{
			case 0: //ISO-8859-1
					return iconv('cp1251', 'UTF-8', substr($tag, 3));
			case 1: //UTF-16 BOM
					return iconv('UTF-16LE', 'UTF-8', substr($tag, 5));
			case 2: //UTF-16BE
					return iconv('UTF-16BE', 'UTF-8', substr($tag, 5));
			case 3: //UTF-8
					return substr($tag, 3);
		}
		return false;
	}
	
	public function read($file){
		$f = fopen($file, 'r');
		$header = fread($f, 10);
		$header = @unpack("a3signature/c1version_major/c1version_minor/c1flags/Nsize", $header);

        if (!$header['signature'] == 'ID3')
		{
			$this->error = 'This file does not contain ID3 v2 tag';		
			fclose($f);
			return false;		
		}

   		$result = array();
		for ($i=0; $i<22; $i++)
		{
			$tag = rtrim(fread($f, 6));
			
			if (!isset($this->tags[$tag])) break;
			
			$size = fread($f, 2);
			$size = @unpack('n', $size);
			$size = $size[1]+2;
	
			$value = fread($f, $size);	
			$value = $this->decTag($value, $tag);
	
			$result[$this->tags[$tag]] = $value;
		}
		
		fclose($f);
  		return $result;	
	}	
}