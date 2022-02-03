<?php
/* 
	Appointment: Проверка файлов на наличие посторонних
	File: antivirus.php
	Author: f0rt1 
	Engine: Vii Engine
	Copyright: NiceWeb Group (с) 2011
	e-mail: niceweb@i.ua
	URL: http://www.niceweb.in.ua/
	ICQ: 427-825-959
	Данный код защищен авторскими правами
*/

class antivirus{
	var $bad_files       = array();
	var $snap_files      = array();
	var $track_files      = array();
	var $snap      		 = false;
	var $checked_folders = array();
	var $dir_split       = '/';

	var $cache_files       = array(
		"./system/cache/system/country.php",
		"./system/cache/system/country_city_.php",
		"./system/cache/system/country_city_1.php",
		"./system/cache/system/country_city_2.php",
		"./system/cache/system/country_city_3.php",
		"./system/cache/system/country_city_4.php",
		"./system/cache/system/country_city_5.php",
		"./system/cache/system/country_city_6.php",
		"./system/cache/system/country_city_7.php",
		"./system/cache/system/country_city_8.php",
		"./system/cache/system/country_city_9.php",
		"./system/cache/system/country_city_10.php",
		"./system/cache/system/country_city_11.php",
		"./system/cache/system/country_city_12.php",
		"./system/cache/system/country_city_13.php",
		"./system/cache/system/country_city_14.php",
		"./system/cache/system/country_city_15.php",
		"./system/cache/system/country_city_16.php",
		"./system/cache/system/country_city_17.php",
		"./system/cache/system/country_city_18.php",
		"./system/cache/system/country_city_19.php",
		"./system/cache/system/country_city_20.php",
		"./system/cache/system/country_city_21.php",
		"./system/cache/system/country_city_22.php",
		"./system/cache/system/country_city_23.php",
		"./system/cache/system/country_city_24.php",
		"./system/cache/system/country_city_25.php",
	);

	var $good_files       = array(
		"./.htaccess",
		"./backup/.htaccess",
		"./system/cache/.htaccess",
		"./system/cache/system/.htaccess",
		"./system/data/.htaccess",
		"./lang/.htaccess",
		"./uploads/.htaccess",
		"./uploads/smiles/.htaccess",
		"./uploads/gifts/.htaccess",
		"./system/classes/antivirus.php",
		"./system/classes/id3v2.php",
		"./system/classes/images.php",
		"./system/classes/mail.php",
		"./system/classes/mysql.php",
		"./system/classes/parse.php",
		"./system/classes/templates.php",
		"./system/classes/wall.php",
		"./system/classes/wall.public.php",
		"./system/data/config.php",
		"./system/data/db.php",
		"./system/inc/antivirus.php",
		"./system/inc/ban.php",
		"./system/inc/db.php",
		"./system/inc/dumper.php",
		"./system/inc/functions.php",
		"./system/inc/gifts.php",
		"./system/inc/groups.php",
		"./system/inc/login.php",
		"./system/inc/mail.php",
		"./system/inc/mail_tpl.php",
		"./system/inc/main.php",
		"./system/inc/massaction.php",
		"./system/inc/mod.php",
		"./system/inc/mysettings.php",
		"./system/inc/notes.php",
		"./system/inc/search.php",
		"./system/inc/static.php",
		"./system/inc/system.php",
		"./system/inc/tpl.php",
		"./system/inc/users.php",
		"./system/inc/videos.php",
		"./system/inc/albums.php",
		"./system/inc/musics.php",
		"./system/inc/stats.php",
		"./system/inc/logs.php",
		"./system/inc/country.php",
		"./system/inc/city.php",
		"./system/modules/albums.php",
		"./system/modules/attach.php",
		"./system/modules/attach_groups.php",
		"./system/modules/audio.php",
		"./system/modules/balance.php",
		"./system/modules/blog.php",
		"./system/modules/editprofile.php",
		"./system/modules/fave.php",
		"./system/modules/friends.php",
		"./system/modules/functions.php",
		"./system/modules/gifts.php",
		"./system/modules/groups.php",
		"./system/modules/gzip.php",
		"./system/modules/im.php",
		"./system/modules/loadcity.php",
		"./system/modules/login.php",
		"./system/modules/messages.php",
		"./system/modules/news.php",
		"./system/modules/notes.php",
		"./system/modules/photo.php",
		"./system/modules/profile.php",
		"./system/modules/public.php",
		"./system/modules/register.php",
		"./system/modules/register_main.php",
		"./system/modules/restore.php",
		"./system/modules/search.php",
		"./system/modules/settings.php",
		"./system/modules/status.php",
		"./system/modules/subscriptions.php",
		"./system/modules/support.php",
		"./system/modules/video.php",
		"./system/modules/videos.php",
		"./system/modules/wall.php",
		"./system/modules/attach_comm.php",
		"./system/modules/groups_forum.php",
		"./system/init.php",
		"./system/mod.php",
		"./badbrowser.php",
		"./controlpanel.php",
		"./index.php",
		"./away.php",
		"./antibot/antibot.php",
		"./antibot/sec_code.php",
		"./system/modules/profile_delet.php",
		"./system/modules/profile_ban.php",
		"./system/modules/offline.php",
		"./system/classes/download.php",
		"./system/inc/report.php",
		"./system/inc/xfields.php",
		"./system/modules/distinguish.php",
		"./system/modules/doc.php",
		"./system/modules/fast_search.php",
		"./system/modules/public_audio.php",
		"./system/modules/report.php",
		"./system/modules/repost.php",
		"./system/modules/static.php",
		"./system/modules/updates.php",
		"./system/modules/votes.php",
		"./uploads/doc/.htaccess",
		"./min/config.php",
		"./min/groupsConfig.php",
		"./min/index.php",
		"./min/lib/FirePHP.php",
		"./min/lib/HTTP/ConditionalGet.php",
		"./min/lib/HTTP/Encoder.php",
		"./min/lib/JSMin.php",
		"./min/lib/JSMinPlus.php",
		"./min/lib/Minify/Build.php",
		"./min/lib/Minify/Cache/APC.php",
		"./min/lib/Minify/Cache/File.php",
		"./min/lib/Minify/Cache/Memcache.php",
		"./min/lib/Minify/CommentPreserver.php",
		"./min/lib/Minify/Controller/Base.php",
		"./min/lib/Minify/Controller/Files.php",
		"./min/lib/Minify/Controller/Groups.php",
		"./min/lib/Minify/Controller/MinApp.php",
		"./min/lib/Minify/Controller/Page.php",
		"./min/lib/Minify/Controller/Version1.php",
		"./min/lib/Minify/CSS/Compressor.php",
		"./min/lib/Minify/CSS/UriRewriter.php",
		"./min/lib/Minify/CSS.php",
		"./min/lib/Minify/HTML.php",
		"./min/lib/Minify/ImportProcessor.php",
		"./min/lib/Minify/Lines.php",
		"./min/lib/Minify/Logger.php",
		"./min/lib/Minify/Packer.php",
		"./min/lib/Minify/Source.php",
		"./min/lib/Minify/YUICompressor.php",
		"./min/lib/Minify.php",
		"./min/lib/Solar/Dir.php",
	);

	function antivirus ()
	{
		if(@file_exists(ENGINE_DIR.'/data/snap.db')) {
  			$filecontents = file(ENGINE_DIR.'/data/snap.db');

		    foreach ($filecontents as $name => $value) {
	    	  $filecontents[$name] = explode("|", trim($value));
	    	    $this->track_files[$filecontents[$name][0]] = $filecontents[$name][1];
		    }
			$this->snap = true;

		}

	}
	
	function scan_files( $dir, $snap = false, $access = false )
	{
		$this->checked_folders[] = $dir . $this->dir_split . $file;
	
		if ( $dh = @opendir( $dir ) )
		{
			while ( false !== ( $file = readdir($dh) ) )
			{
				if ( $file == '.' or $file == '..' or $file == '.svn' or $file == '.DS_store' )
				{
					continue;
				}
		
				if ( is_dir( $dir . $this->dir_split . $file ) )
				{

					if ($dir != ROOT_DIR)
					$this->scan_files( $dir . $this->dir_split . $file, $snap, $access );
				}
				else
				{

					if ($this->snap OR $snap) $templates = "|tpl|js|lng|htaccess"; elseif($access) $templates = "|htaccess"; else $templates = "";

					if ( preg_match( "#.*\.(php|cgi|pl|perl|php3|php4|php5|php6".$templates.")#i", $file ) )
					{

					  $folder = str_replace(ROOT_DIR, ".",$dir);
					  $file_size = filesize($dir . $this->dir_split . $file);
					  $file_crc = md5_file($dir . $this->dir_split . $file);
					  $file_date = date("d.m.Y H:i:s", filectime($dir . $this->dir_split . $file));

					  if ($snap) {

						$this->snap_files[] = array( 'file_path' => $folder . $this->dir_split . $file,
													 'file_crc' => $file_crc );


                      } else {

						if ($this->snap) {


							if ($this->track_files[$folder . $this->dir_split . $file] != $file_crc AND !in_array($folder . $this->dir_split . $file, $this->cache_files))
							$this->bad_files[] = array( 'file_path' => $folder . $this->dir_split . $file,
													'file_name' => $file,
													'file_date' => $file_date,
													'type' => 1,
													'file_size' => $file_size );

					    } else { 

						 if (!in_array($folder . $this->dir_split . $file, $this->good_files))
						 $this->bad_files[] = array( 'file_path' => $folder . $this->dir_split . $file,
													'file_name' => $file,
													'file_date' => $file_date,
													'type' => 0,
													'file_size' => $file_size ); 

						}

					  }
					}
				}
			}
		}
	}
}

?>