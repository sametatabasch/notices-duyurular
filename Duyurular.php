<?php
/*
    Plugin Name: Duyurular
    Plugin URI: http://www.gençbilişim.net
    Description: Gençbilişim Duyurular
    Author: Samet ATABAŞ
    Version: 1.0
    Author URI: http://www.gençbilişim.net
*/

/**
* Duyurular class ı 
* @author Samet ATABAŞ
*
*/
class Duyurular{
	/**
	* eklenti dizinini tutar
	* @var path string
	*/
	private $path;
	function __construct() {
		//eklenti dizinini tanımla
		$this->path = plugin_dir_url(__FILE__);
		//duyurular için Duyuru  post type ını ekle
		add_action( 'init', array(&$this , 'postTypeOlustur'));
		// ayar sayfasını ekle
		add_action('admin_menu', array(&$this, 'ayarSayfası'));
		// yazı  editorü sayfasına widget ekleme
		add_action( 'add_meta_boxes', array(&$this, 'duyuruMetaBoxEkle'));
		// duyuru kaydedildiği zaman meta box taki  verileri işlemek için kullanılır
		add_action( 'save_post', array(&$this, 'duyuruOlustur'));
		// duyuru düzenlerdiği zaman meta box taki  verileri işlemek için kullanılır
		add_action( 'edit_post', array(&$this, 'duyuruDuzenle'));
		add_action('init',array(&$this,'duyuruGoster'));
	}
	
	/**
	* Post Type oluşturan fonksiyon
	*
	* @return bollean
	*/
	public function postTypeOlustur() {
		register_post_type( 'Duyuru',
			array(
				'labels' => array(/*labels kullanılan başlıkları belirlemeye yarıyor*/
					'name' =>  'Duyuru' ,
					'singular_name' =>  'Duyuru',
					/*'add_new' => _x('Add New', 'book'), çoklu  dil  için örnek*/
					'add_new' => 'Yeni Duyuru',
    				'add_new_item' => 'Yeni Duyuru Ekle',
    				'edit_item' => 'Duyuruyu Düzenle',
    				'new_item' => 'Yeni Duyuru',
    				'all_items' => 'Tüm Duyurular',
    				'view_item' => 'Duyuruyu Göster',
    				'search_items' => 'Duyuru Ara',
    				'not_found' =>  'Duyuru Bulunamadı',
    				'not_found_in_trash' => 'Silinen Duyuru Yok', 
    				'parent_item_colon' => '',
    				'menu_name' => 'Duyurular'
				),
			'public' => false,
			'has_archive' => true,
			'show_ui' => true, 
    		'show_in_menu' => true,
			)
		);
	}
	/**
	* Duyuru bilgilerini veritabanından alan fonksiyon
	* tüm duyuruları enson yazılan ilk olacak şekilde dizi içinde saklar
	* Qreturn array
	*/
	public static function duyuruMeta() {
		global $wpdb;
		return  $wpdb->get_results("SELECT * FROM $wpdb->posts WHERE post_type='duyuru' AND post_status='publish' ORDER BY post_date_gmt DESC", 'ARRAY_A');
		
	}
	/**
	* Duyuruyu metnini gösterecek fonksiyon
	*
	* @return string
	*/
	public static function duyuruMetni() {
		$metin=self::duyuruMeta();
		echo $metin[0]['post_content'];
	}
	/**
	* Duyuruyu tarihi gösterecek fonksiyon
	*
	* @return string
	*/
	public static function duyuruTarihi() {
		$tarih=self::duyuruMeta();
		$tarih=str_replace('-', '', $tarih[0]["post_date_gmt"]);
		echo substr($tarih,6,2).'.'.substr($tarih,4,2).'.'.substr($tarih,0,4);
	}
	/**
	 * duyuruGoster fonksiyonu 
	 * duyurunun gösterim tarihine kimlerin göreceğine ve nasıl görüneceğine göre duyuruyu gösteren fonksiyon
	 *
	 */
	public function duyuruGoster(){
		$duyuru=self::duyuruMeta();		
		if(get_post_meta($duyuru[0]['ID'],"kimlerGorsun",1)=="herkes") {
			//echo  'herkes görsün';
		}
		
	}
	/**
	* Ayarsayfası oluştur
	* 
	* @return void
	*/
	public function ayarSayfası() {
		add_options_page('Duyurular ', 'Duyurular ', 'manage_options', 'duyurular', array(&$this,'ayarSayfasiIcerik'));
	}
	/**
	* Ayar sayfasının içeriği bu sayfa üzerinden belirleinyor
	*
	*
	*
	*/
	public function ayarSayfasiIcerik() {
		echo 'Ayar sayfası';
	}
	/**
	* Duyuru meta box ekler
	*
	*/
	public function duyuruMetaBoxEkle() {
		add_meta_box( 'duyuruMetaBox', 'Duyuru ayarları', array(&$this,'duyuruMetaBox'), 'Duyuru', 'side', 'default', $callback_args );
	}
	/**
	* duyuruMetaBox fonksiyonu 
	* Duyuru oluşturma ve düzenleme sayfasına ayarlamalar için widget içeriği
	*
	*/
	public function duyuruMetaBox() {
		global $post_id;
		$kimlerGorsun=get_post_meta($post_id,"kimlerGorsun",1);
		$gosteriModu=get_post_meta($post_id,"gosteriModu",1);
		?> 
		<form>
		Kimler görsün:
		<select name="kimlerGorsun">
			<option  <?php  if($kimlerGorsun=='herkes') {echo 'selected=""';} ?>  value="herkes">Herkes</option>
			<option <?php  if($kimlerGorsun=='uyeler') {echo 'selected=""';} ?> value="uyeler">Sadece Üyeler</option>
		</select>
		Gösterim Modu:
		<select name="gosterimModu">
			<option <?php  if($gosterimModu=='pencere') {echo 'selected=""';} ?> value="pencere">Pencere</option>
			<option <?php  if($gosteriModu=='bar') {echo 'selected=""';} ?> value="bar">Uyarı Şeridi</option>
		</select>
		</form>
		<?php
	}
	/**
	* Duyuru  Meta box  içeriğindeki verileri alıp  işleyerek  duyuruyo oluştururken ek işlenmleri yapacak 
	* Post ile verileri alacak
	*
	*/
	public function duyuruOlustur() {
		global $post_id;
		$kimlerGorsun=$_POST["kimlerGorsun"];
		$gosteriModu=$_POST["gosterimModu"];
		add_post_meta($post_id, "kimlerGorsun", $kimlerGorsun,true);
		add_post_meta($post_id, "gosteriModu", $gosteriModu,true);
	}
	/**
	 * duyuru  güncellendiği zaman yapılacak  olan düzenlemeler bu  fonksiyonile yapılıyor
	 *
	 */
	public function duyuruDuzenle() {
		global $post_id;
		$kimlerGorsun=$_POST["kimlerGorsun"];
		$gosteriModu=$_POST["gosterimModu"];
		update_post_meta($post_id, "kimlerGorsun", $kimlerGorsun);
		update_post_meta($post_id, "gosteriModu", $gosteriModu);
	}
}
$duyuru= new Duyurular();
?>