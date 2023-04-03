<?php
namespace App\Helpers;

use App\DataMappers\Booking\Address;
use Haversini\Haversini;


class GeneralHelpers
{
    public static  function RandomId()
    {
        return sprintf('%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
    }
    public static function FileUploader($destination,$file,$fileName = null)
    {
          if($fileName == 'rand')
          {
            $fileName = self::RandomId();
          }
          //Display File Name
          $imageTypes = ['jpg','jpeg','png','svg'];
          $docsTypes = ['xlsx','pdf','csv','php','docs','docx','doc','txt','sql','html'];
          $objFile['success'] = 0;
          if(in_array($file->getClientOriginalExtension(), $docsTypes))
          {
                $objFile['file_orignal_name'] = $file->getClientOriginalName() ;
                $objFile['file_extension']    = $file->getClientOriginalExtension();
                $objFile['file_name']         = time().'_'.rand(0,100).'_'.$objFile['file_orignal_name'];
                $objFile['file_real_path']    = $file->getRealPath();
                $objFile['file_size']         = $file->getSize();

          }else if(in_array($file->getClientOriginalExtension(), $imageTypes))
          {
                $objFile['file_orignal_name'] = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME); ;
                $objFile['file_extension']    = $file->getClientOriginalExtension();
                $objFile['file_name']         = time().'_'.$objFile['file_orignal_name'].'_'.rand(0,100).'.'.$objFile['file_extension'];
                $objFile['file_real_path']    = $file->getRealPath();
                $objFile['file_size']         = $file->getSize();
                $objFile['file_mime']         = $file->getMimeType();
          }else
          {
                $objFile['file_orignal_name'] = $file->getClientOriginalName() ;
                $objFile['file_extension']    = $file->getClientOriginalExtension();
                $objFile['file_name']         = time().'_'.rand(0,100).'_'.$objFile['file_orignal_name'];
                $objFile['file_real_path']    = $file->getRealPath();
                $objFile['file_size']         = $file->getSize();
          }


          if(!empty($fileName))
                $objFile['file_name'] = time().'_'.$fileName.'_'.rand(0,100).'.'.$objFile['file_extension'];
          //Move Uploaded File
          $objFile['file_type']               =   self::getFileType($objFile['file_extension']);
          $objFile['destination_path']        =   $destination . '/' ;
          $objFile['uploaded_path']           =   $destination . '/' . $objFile['file_name'];

          $result = $file->move(self::PUBLIC_UPLOAD_PATH() . '/' .$objFile['destination_path'],$objFile['file_name']);
          if($result)
          {
                $objFile['success'] = 1;
          }

          return $objFile;
    }
    public static function PUBLIC_UPLOAD_PATH()
    {
          return 'uploads';
    }
    public static function getFileType($fileExtension)
    {
          $arr = [
                'jpg'       => 'image',
                'jpeg'      => 'image',
                'png'       => 'image',
                'svg'       => 'image',

                'xlsx'      => 'docx',
                'pdf'       => 'pdf',
                'csv'       => 'docx',
                'docs'      => 'docx',
                'docx'      => 'docx',
                'doc'       => 'docx',

                'txt'       => 'code',
                'php'       => 'code',
                'sql'       => 'code',
                'html'      => 'code',
          ];

          if(isset($arr[$fileExtension]))
          {
                return $arr[$fileExtension];
          }else
          {
                return 'others';
          }
    }
     
    public static function getPhoneCode()
    {


        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => 'http://country.io/phone.json',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'GET',
      ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;

    }
    public static function SeoFriendlySlug($slug)
    {
        $stopWords = ['A','ABOUT','ACTUALLY','ALMOST','ALSO','ALTHOUGH','ALWAYS','AM','AN','AND','ANY','ARE','AS','AT','BE','BECAME','BECOME','BUT','BY','CAN','COULD','DID','DO','DOES','EACH','EITHER','ELSE','FOR','FROM','HAD','HAS','HAVE','HENCE','HOW','I','IF','IN','IS','IT','ITS','JUST','MAY','MAYBE','ME','MIGHT','MINE','MUST','MY','MINE','MUST','MY','NEITHER','NOR','NOT','OF','OH','OK','WHEN','WHERE','WHEREAS','WHEREVER','WHENEVER'];

        foreach ($stopWords as $word) 
        {
            $slug = str_replace($word, "", $slug);
        }
        return $slug;
    }



    public static function getCountryCodeByName($countryName)
    {
        foreach(self::countryList() as $code => $name)
        {
            if($name == $countryName)
            {
                return $code;
            }
        }

        return 'GB';
    }

    public static function countryList($countryCode = null)
    {
        $arr = [];

        $arr["AU"] = "Australia";
        $arr["CA"] = "Canada";
        $arr["CN"] = "China";
        $arr["FR"] = "France";
        $arr["DE"] = "Germany";
        $arr["IT"] = "Italy";
        $arr["JP"] = "Japan";
        $arr["ES"] = "Spain";
        $arr["GB"] = "United Kingdom";
        $arr["US"] = "United States"; 

        $arr["af"] = "Afghanistan";
        $arr["al"] = "Albania";
        $arr["dz"] = "Algeria";
        $arr["as"] = "American Samoa";
        $arr["ad"] = "Andorra";
        $arr["ao"] = "Angola";
        $arr["ai"] = "Anguilla";
        $arr["ag"] = "Antigua &amp; Barbuda";
        $arr["ar"] = "Argentina";
        $arr["am"] = "Armenia";
        $arr["aw"] = "Aruba";
        $arr["au"] = "Australia";
        $arr["at"] = "Austria";
        $arr["az"] = "Azerbaijan";
        $arr["bs"] = "Bahamas";
        $arr["bh"] = "Bahrain";
        $arr["bd"] = "Bangladesh";
        $arr["bb"] = "Barbados";
        $arr["by"] = "Belarus";
        $arr["be"] = "Belgium";
        $arr["bz"] = "Belize";
        $arr["bj"] = "Benin";
        $arr["bm"] = "Bermuda";
        $arr["bt"] = "Bhutan";
        $arr["bo"] = "Bolivia";
        $arr["ba"] = "Bosnia &amp; Herzegovina";
        $arr["bw"] = "Botswana";
        $arr["br"] = "Brazil";
        $arr["vg"] = "British Virgin Islands";
        $arr["bn"] = "Brunei";
        $arr["bg"] = "Bulgaria";
        $arr["bf"] = "Burkina Faso";
        $arr["bi"] = "Burundi";
        $arr["kh"] = "Cambodia";
        $arr["cm"] = "Cameroon";
        $arr["ca"] = "Canada";
        $arr["ky"] = "Cayman Islands";
        $arr["cf"] = "Central African Republic";
        $arr["td"] = "Chad";
        $arr["cl"] = "Chile";
        $arr["cn"] = "China";
        $arr["co"] = "Colombia";
        $arr["km"] = "Comoros";
        $arr["cg"] = "Congo - Brazzaville";
        $arr["cd"] = "Congo - Kinshasa";
        $arr["ck"] = "Cook Islands";
        $arr["cr"] = "Costa Rica";
        $arr["ci"] = "Côte d’Ivoire";
        $arr["hr"] = "Croatia";
        $arr["cw"] = "Curaçao";
        $arr["cy"] = "Cyprus";
        $arr["cz"] = "Czech Republic";
        $arr["dk"] = "Denmark";
        $arr["dj"] = "Djibouti";
        $arr["dm"] = "Dominica";
        $arr["do"] = "Dominican Republic";
        $arr["ec"] = "Ecuador";
        $arr["eg"] = "Egypt";
        $arr["sv"] = "El Salvador";
        $arr["gq"] = "Equatorial Guinea";
        $arr["er"] = "Eritrea";
        $arr["ee"] = "Estonia";
        $arr["et"] = "Ethiopia";
        $arr["fk"] = "Falkland Islands";
        $arr["fo"] = "Faroe Islands";
        $arr["fj"] = "Fiji";
        $arr["fi"] = "Finland";
        $arr["fr"] = "France";
        $arr["gf"] = "French Guiana";
        $arr["pf"] = "French Polynesia";
        $arr["ga"] = "Gabon";
        $arr["gm"] = "Gambia";
        $arr["ge"] = "Georgia";
        $arr["de"] = "Germany";
        $arr["gi"] = "Gibraltar";
        $arr["gr"] = "Greece";
        $arr["gl"] = "Greenland";
        $arr["gd"] = "Grenada";
        $arr["gp"] = "Guadeloupe";
        $arr["gt"] = "Guatemala";
        $arr["gg"] = "Guernsey";
        $arr["gn"] = "Guinea";
        $arr["gw"] = "Guinea-Bissau";
        $arr["gy"] = "Guyana";
        $arr["ht"] = "Haiti";
        $arr["hn"] = "Honduras";
        $arr["hk"] = "Hong Kong SAR China";
        $arr["hu"] = "Hungary";
        $arr["is"] = "Iceland";
        $arr["in"] = "India";
        $arr["id"] = "Indonesia";
        $arr["iq"] = "Iraq";
        $arr["ie"] = "Ireland";
        $arr["il"] = "Israel";
        $arr["it"] = "Italy";
        $arr["jm"] = "Jamaica";
        $arr["jp"] = "Japan";
        $arr["je"] = "Jersey";
        $arr["jo"] = "Jordan";
        $arr["kz"] = "Kazakhstan";
        $arr["ke"] = "Kenya";
        $arr["xk"] = "Kosovo";
        $arr["kw"] = "Kuwait";
        $arr["kg"] = "Kyrgyzstan";
        $arr["la"] = "Laos";
        $arr["lv"] = "Latvia";
        $arr["lb"] = "Lebanon";
        $arr["ls"] = "Lesotho";
        $arr["lr"] = "Liberia";
        $arr["ly"] = "Libya";
        $arr["li"] = "Liechtenstein";
        $arr["lt"] = "Lithuania";
        $arr["lu"] = "Luxembourg";
        $arr["mo"] = "Macau SAR China";
        $arr["mk"] = "Macedonia";
        $arr["mg"] = "Madagascar";
        $arr["mw"] = "Malawi";
        $arr["my"] = "Malaysia";
        $arr["mv"] = "Maldives";
        $arr["ml"] = "Mali";
        $arr["mt"] = "Malta";
        $arr["mq"] = "Martinique";
        $arr["mr"] = "Mauritania";
        $arr["mu"] = "Mauritius";
        $arr["yt"] = "Mayotte";
        $arr["mx"] = "Mexico";
        $arr["md"] = "Moldova";
        $arr["mc"] = "Monaco";
        $arr["mn"] = "Mongolia";
        $arr["me"] = "Montenegro";
        $arr["ms"] = "Montserrat";
        $arr["ma"] = "Morocco";
        $arr["mz"] = "Mozambique";
        $arr["mm"] = "Myanmar (Burma)";
        $arr["na"] = "Namibia";
        $arr["np"] = "Nepal";
        $arr["nl"] = "Netherlands";
        $arr["nc"] = "New Caledonia";
        $arr["nz"] = "New Zealand";
        $arr["ni"] = "Nicaragua";
        $arr["ne"] = "Niger";
        $arr["ng"] = "Nigeria";
        $arr["no"] = "Norway";
        $arr["om"] = "Oman";
        $arr["pk"] = "Pakistan";
        $arr["ps"] = "Palestinian Territories";
        $arr["pa"] = "Panama";
        $arr["pg"] = "Papua New Guinea";
        $arr["py"] = "Paraguay";
        $arr["pe"] = "Peru";
        $arr["ph"] = "Philippines";
        $arr["pl"] = "Poland";
        $arr["pt"] = "Portugal";
        $arr["pr"] = "Puerto Rico";
        $arr["qa"] = "Qatar";
        $arr["re"] = "Réunion";
        $arr["ro"] = "Romania";
        $arr["ru"] = "Russia";
        $arr["rw"] = "Rwanda";
        $arr["ws"] = "Samoa";
        $arr["sm"] = "San Marino";
        $arr["sa"] = "Saudi Arabia";
        $arr["sn"] = "Senegal";
        $arr["rs"] = "Serbia";
        $arr["sc"] = "Seychelles";
        $arr["sl"] = "Sierra Leone";
        $arr["sg"] = "Singapore";
        $arr["sx"] = "Sint Maarten";
        $arr["sk"] = "Slovakia";
        $arr["si"] = "Slovenia";
        $arr["sb"] = "Solomon Islands";
        $arr["so"] = "Somalia";
        $arr["za"] = "South Africa";
        $arr["gs"] = "South Georgia &amp; South Sandwich Islands";
        $arr["kr"] = "South Korea";
        $arr["es"] = "Spain";
        $arr["ES-IB"] = "Spain - Balearic Islands";
        $arr["ES-CN"] = "Spain - Canary Islands";
        $arr["lk"] = "Sri Lanka";
        $arr["bl"] = "St. Barthélemy";
        $arr["sh"] = "St. Helena";
        $arr["kn"] = "St. Kitts &amp; Nevis";
        $arr["lc"] = "St. Lucia";
        $arr["mf"] = "St. Martin";
        $arr["pm"] = "St. Pierre &amp; Miquelon";
        $arr["vc"] = "St. Vincent &amp; Grenadines";
        $arr["sr"] = "Suriname";
        $arr["sz"] = "Swaziland";
        $arr["se"] = "Sweden";
        $arr["ch"] = "Switzerland";
        $arr["tw"] = "Taiwan";
        $arr["tj"] = "Tajikistan";
        $arr["tz"] = "Tanzania";
        $arr["th"] = "Thailand";
        $arr["tl"] = "Timor-Leste";
        $arr["tg"] = "Togo";
        $arr["to"] = "Tonga";
        $arr["tt"] = "Trinidad &amp; Tobago";
        $arr["tn"] = "Tunisia";
        $arr["tr"] = "Turkey";
        $arr["tm"] = "Turkmenistan";
        $arr["tc"] = "Turks &amp; Caicos Islands";
        $arr["vi"] = "U.S. Virgin Islands";
        $arr["ug"] = "Uganda";
        $arr["ua"] = "Ukraine";
        $arr["ae"] = "United Arab Emirates";
        $arr["gb"] = "United Kingdom";
        $arr["us"] = "United States";
        $arr["uy"] = "Uruguay";
        $arr["uz"] = "Uzbekistan";
        $arr["vu"] = "Vanuatu";
        $arr["ve"] = "Venezuela";
        $arr["vn"] = "Vietnam";
        $arr["eh"] = "Western Sahara";
        $arr["ye"] = "Yemen";
        $arr["zm"] = "Zambia";
        $arr["zw"] = "Zimbabwe";

        if($countryCode != null)
        {
            return $arr[$countryCode];
        }

        return $arr; 

    }

    
}