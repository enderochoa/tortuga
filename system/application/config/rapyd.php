<?php
/*
|--------------------------------------------------------------------------
| replace function argument separator
|--------------------------------------------------------------------------
|
| Into your pattern expressions (in grid and fields, see the rapyd guide)
| You can change the argument separator string "|" by an other one, it will be usefull if you want to use the "|" caractere
| into the argument itself.
| $this->rapyd->config->set_item("argument_separator","%%");   
*/
$rpd['argument_separator']="|";

$rpd['data_conn'] = '';
/*
|--------------------------------------------------------------------------
| view theme folder
|--------------------------------------------------------------------------
|
| (folder name)  /application/rapyd/views/[theme]/component.php
| You may develop your themes, copy and paste the default theme then change at least the CSS, and then set here the theme
| You chan change themplate at runtime for example in a controller you can do:
| $this->rapyd->config->set_item("theme","mytheme");   
*/

//$rpd['theme'] = 'default';
$rpd['theme'] = 'proteo';


/*
|--------------------------------------------------------------------------
| images and libraries "base path"
|--------------------------------------------------------------------------
|
| rapyd need some icons/images, third party libs (javascript)and now css (after version 0.9.7) for his components, so it need some public folders.
| Images and Css are a little bit different than libraries;
|
| libraries:
| by default the folders is /application/rapyd/libraries
| 
| Images and Css:
| Now Imges and Css are grouped into an elements directory following a particular structure.
| by default the 'elementse folders is /application/rapyd/elements
| The directory structure:
| Into the 'elements' directory we have the Theme directory (name of this directory have to match with the Rapyd views Theme name except for the shared one)
| And in each 'elements/theme' directory we have one folder for the images and an other for the css. By default images folder name is 'images' and CSS 
| folder name is 'css' but this name could be change in this config by the 'assets_type_folder_name' conf.
|
| normally you need to change this configuration if CI is outside the website root
| in this case you need to move rapyd elements and libraries folder in a accessible path, and set an absolute path like:
| $rpd['design_elements_path'] = '/rapydelments/';
| $rpd['libraries_path'] = '/citest/rapydlibs/';
|
| and/or if you use revrite roules, you may need to add in this folder an .htaccess file like this:
|  <IfModule mod_rewrite.c>
|    RewriteEngine off
|  </IfModule>
|
| you can't use rapyd constants here!
| the path must be absolute!
| leave it blank if you don't move rapyd image folder outside rapyd folder.
*/
$rpd['libraries_path'] = '';

$rpd['design_elements_path'] ="";
//We can change the default name for the assets folder here but all theme design have to use the same
//asset folder name for each type.....
$rpd['assets_type_folder_name'] =array('css' => 'css','image'=>'images');// should be array('css' => 'css folder name','image'=>'images folder name')

//The shared theme design have to respect all the 'elements/theme' directory restriction, except that it can NOT correspond to a Rapyd Theme
//The rapyd design system always look first into the current elements->Theme directory (for css and images) And if the requested file is missing
//It should look into the elements->Shared_Theme directory.And if the file is also missing in the shared directory it take the previous path
$rpd['shared_theme_design']='default';


/*
|--------------------------------------------------------------------------
| Authentication system
|--------------------------------------------------------------------------
| You can simply enable or desable the authentication class loading
|*/

$rpd['rapyd_auth_ON']= true;



/*
|--------------------------------------------------------------------------
| default language (buttons & messages)
|--------------------------------------------------------------------------
| rapyd need some messages for his components, so to keep multilanguage support, you need to set a language string to load constants file/files.
|
*/

//MODIF Lang 
$rpd['rapyd_lang_ON']= false;
$rpd['language'] = 'english';


/*
|--------------------------------------------------------------------------
| languages for internationalization (see rapyd_lang class)
|--------------------------------------------------------------------------
| if your application is multilanguage, you can specify accepted languages.
|
| note: CI language files & rapyd language files "are needed" for all language you decide to use
| by default CI include only english language; by default rapyd include english,italian,french,german,spanish.
*/

$rpd['languages'] = array(
  'en'    => 'english',
  'it'    => 'italian',
  'fr'    => 'french',
  'de'    => 'german',
  'es'    => 'spanish',
  );

$rpd['browser-detect'] = true;
$rpd['browser-to-language'] = array(
	'us'    => 'english',
  'en'    => 'english',
  'it'    => 'italian',
  'fr'    => 'french',
  'de'    => 'german',
  //'ru'    => 'russian',
  'es'    => 'spanish',

);


$rpd['ip-detect'] = false;
$rpd['country-to-language'] = array(
    'ITA'=>'italian',
    'USA'=>'english',
    'GBR'=>'english',
    //...
);

/*
|--------------------------------------------------------------------------
| keep language persistence in uri
|--------------------------------------------------------------------------
| it simply add, when you use anchor_lang (or anchor_popup_lang), the language segment first:
| anchor_lang("controller/function/param")  it's equal to anchor("{current_lang}/controller/function/param")
|
| It need to be used in conjunction with a custom uri sniff/reindex function.
*/ 
$rpd['uri_keep_lang'] = TRUE;


/*
|--------------------------------------------------------------------------
| rapyd_uri keywords
|--------------------------------------------------------------------------
| rapyd components need to reserve some words to manage application flow
| by default these keywords are simples and thinked to keep your urls really semantic:
| ../orderby/creation/desc/search
| ../uri_search/genre/female
| ../modify/10
| ../delete/5
| 
| but this mean that these words are reserved, if you need to use a rapyd component..
| you can't use give to a controller, method, or parameter the same name.
|
| Here you can configure/change some of these keywords.
| Important: use minuscase, CHANGE ONLY THE VALUES not keys, each uri_keyword need to be "unique" in your application uri.
*/

$rpd['uri_keywords'] = array(

     //rapyd_uri
    'gfid'        => 'gfid',
    
     //datefilter
    'search'      => 'search',
    'reset'       => 'reset',
    'uri_search'  => 'uri_search',

     //dataset / datagrid / datatable
    'osp'         => 'osp',
    'orderby'     => 'orderby',

    //dataedit
    'show'        => 'show',
    'create'      => 'create',
    'modify'      => 'modify',
    'delete'      => 'delete',
    'insert'      => 'insert',
    'update'      => 'update',
    'do_delete'   => 'do_delete'
    
  );


/*
|--------------------------------------------------------------------------
| session/persistence settings
|--------------------------------------------------------------------------
|
| Rapyd can save in $_SESSION each page/controller  status ($_POST, uri segments)
| It make possible to move across pages and keep persistence of rapyd components.
| How it work:
| When a component (Like a DataGrid) build detail links, pagination links, add buttons etc. it add a pair of segments: 
| ..youruri/gfid/{identifier}  The identifier is an index of a session var which keep "back_uri" and "back_post"
| So a component in anhoter page can bo back to the prev page, retriving the same component status.
*/
//MODIF 1.6
//--- Determine if we use the Rapyd native session lib or the CI one. If you use the Rapyd one This mean that if your application use CI session 
//--- It will work with 2 sessions lib loaded, it work weell but slow down your app because we load and use 2 lib.
//--- If you have a CI session compatible with Rapyd interface (a CI MY_Session.php extended the CI_Sessionis needed) you can set it FALSE this way
//--- you will load and use only one session lib.You can use MYFW Dd_session or Php_session lib.

$rpd['use_rapyd_session_lib'] = true;

$rpd['persistence_duration'] = 241;  //max persistence seconds
$rpd['persistence_limit'] = 10;  //max number of concurrent sessions per uri.




/*
|--------------------------------------------------------------------------
| replace_functions 
|--------------------------------------------------------------------------
|
| is an array of php functions that can be used when a rapyd component parse the his content
|
| for example in a datagrid you can use the susbstr function to get the fists 100 chars of body field:
| $datagrid = new DataGrid();
| $datagrid->base_url = site_url('controller/function');
| $datagrid->per_page = 2;
| $datagrid->column("Title","title");
| //here I use a replace function (substr).  Note  the parameters, they are joined by | (pipe)
| $datagrid->column("Body", "<substr><#body#>|0|100</substr>..");
| $datagrid->build();
|
*/
$rpd['replace_functions'] = array(
  "htmlspecialchars","htmlentities","strtolower","strtoupper",
  "substr","nl2br","dbdate_to_human", "number_format", "raencode","nformat","moneyformat",
  "enum_to_human","wordwrap");



/*
|--------------------------------------------------------------------------
| Encryption Key
|--------------------------------------------------------------------------
|
| For security reason, some rapyd components use CI Encryption class (i.e. rapyd_session)
| note, I've choosen to place encryption here instead using CI config key for compatibility reason.
| See: http://www.codeigniter.com/user_guide/libraries/encryption.html
*/
$rpd['encryption_key'] = "rapydencryptionkey";  //Important: please replace with your custom encryption_key.
  
?>
