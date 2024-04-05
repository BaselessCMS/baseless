<?php defined('BLUDIT') or die('Bludit CMS.'); ?>
{
	"system" : {
		"version" : "3.15.0",
		"build"   : "20230715",
		"date"    : "2023-07-15"
	},
	"debug" : {
		"mode"   : true,
		"type"   : "INFO",
		"errors" : 1,
		"start"  : 0,
		"html"   : 1,
		"log"    : 1,
		"file"   : "debug.txt"
	},
	"log" : {
		"sep"   : " | ",
		"info"  : "[INFO]",
		"warn"  : "[WARN]",
		"error" : "[ERROR]"
	},
	"symlink" : true,
	"alert" : {
		"ok"      : 0,
		"fail"    : 1,
		"seconds" : 4
	},
	"charset" : "UTF-8",
	"default_lang" : "en.json",
	"dir_permission" : "0755",
	"login" : {
		"pw_length"     : 6,
		"salt_length"   : 8,
		"cookie_user"   : "CMSREMEMBERUSERNAME",
		"cookie_token"  : "CMSREMEMBERTOKEN",
		"cookie_expire" : 30,
		"email_token"   : "+15 minutes",
		"session_life"  : 0,
		"session_max"   : 3600
	},
	"dash_notify_qty" : 10,
	"dates" : {
		"db"       : "Y-m-d H:i:s",
		"backup"   : "Y-m-d-H-i-s",
		"sitemap"  : "m-d-Y",
		"admin"    : "M j Y, H:i",
		"schedule" : "D, M j Y, H:i",
		"notify"   : "M j Y, H:i",
		"manage"   : "M j Y, H:i"
	},
	"media" : {
		"img_ext"   : ["gif","png","jpg","jpeg","svg","webp"],
		"img_mime"  : ["image/gif","image/png","image/jpeg","image/svg+xml","image/webp"],
		"per_page"  : 5,
		"sort_date" : true,
		"avatar_width"   : 640,
		"avatar_height"  : 640,
		"avatar_quality" : 100
	},
	"content" : {
		"db_file"    : "index.txt",
		"tags_types" : ["published","static","sticky"],
		"page_break" : "<!-- pagebreak -->"
	},
	"admin" : {
		"uri" : "admin",
		"per_page" : 20
	},
	"theme" : {}
}
