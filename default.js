/**
 * pencere modundaki duyuruları gösterecek fonksiyon
 */
//todo Window sınıfı ile oluşturulan pencelerilerin birbirinden ayrıması için bir id oluşturup bu id eklenen divlere eklenmeli
//todo pencereden evet yada hayır şeklinde bir dönüt almak için callback tanımlamak bunun için birşeyler yapmak lazım
jQuery.fn.Window = function (content, isClass) {
	this.currentIndex = 0;
	this.getContent = function () {
		isClass ? this.content = jQuery(content) : this.content = content;
		console.log(this.content);
		if (isClass) this.content.remove();//window class ına sahip nesneleri sayfadan temizledik
	}
	/**
	 * sayfadaki  konumu  yeniden  düzenler
	 */
	this.reLocate = function () {
		jQuery('.window').css({'max-height': (window.innerHeight / 2), 'max-width': (window.innerWidth / 2)});
		var windowBoxWidth = jQuery('#windowBox').width();
		var windowBoxHeight = jQuery('#windowBox').height();
		var windowBoxLeft = (window.innerWidth - windowBoxWidth) / 2;
		var windowBoxTop = (window.innerHeight - windowBoxHeight) / 2;
		jQuery('#windowBox').css({
			'left': windowBoxLeft,
			'top' : windowBoxTop
		});
	};

	/**
	 * bir önceki  duyuruyu getirir
	 *
	 */
	this.next = function () {
		this.currentIndex--;
		if (this.currentIndex < 0) this.currentIndex = this.content.length - 1;
		jQuery('#windowBox').fadeOut(jQuery.proxy(function () {
			jQuery('#windowBox').find('.window').replaceWith(this.content[this.currentIndex]);
			jQuery('#windowBox').css({'display': 'block'});
			jQuery('.window .close').click(jQuery.proxy(function () {
				this.close();
			}, this));
			this.reLocate();
		}, this));
	};

	/**
	 * sonraki duyuruyu getirir
	 *
	 */
	this.prev = function () {
		this.currentIndex++;
		if (this.currentIndex > this.content.length - 1) this.currentIndex = 0;
		jQuery('#windowBox').fadeOut(jQuery.proxy(function () {
			jQuery('#windowBox').css({'display': 'block'});
			jQuery('#windowBox').find('.window').replaceWith(this.content[this.currentIndex]);
			jQuery('.window .close').click(jQuery.proxy(function () {
				this.close();
			}, this));
			this.reLocate();
		}, this));
	};
	/**
	 * pencereyi ekranda gösterir
	 */
	this.show = function () {
		this.getContent();
		jQuery('body').append('<div id="windowBackground"><div class="windowBackground"></div></div>');
		jQuery('#windowBackground').append('<div id="windowBox" class=""></div>');//window class lı nesnenin ekleneceği div eklendi
		if (isClass) {
			jQuery('#windowBox').append(this.content[this.currentIndex]);
		} else {
			jQuery('#windowBox').append(this.content);
		}
		if (this.content.length > 1 && isClass) {
			jQuery('#windowBox').append('<a href="javascript:;" class="window-nav window-nav-previous" title="Previous"><span></span></a>');
			jQuery('#windowBox').append('<a href="javascript:;" class="window-nav window-nav-next" title="Next"><span></span></a>');
			jQuery('.window-nav-previous').click(jQuery.proxy(function () {
				this.prev();
			}, this));
			jQuery('.window-nav-next').click(jQuery.proxy(function () {
				this.next();
			}, this));
		}
		jQuery('.window .close').click(jQuery.proxy(function () {
			this.hide();
		}, this));
		//arka plana tıklayınca silinsin
		jQuery('.windowBackground').click(jQuery.proxy(function () {
			this.close();
		}, this));
		this.reLocate();
	};
	this.hide = function () {
		currnetId = this.content[this.currentIndex].id;
		this.content.splice(this.currentIndex, 1);
		denemeWindow.show();
		close(jQuery('.window .close').parent());
		if (this.content.length > 0) {
			this.next();
		} else {
			close(jQuery('#windowBackground'));
		}
	}
	this.close = function () {
		close(jQuery('#windowBackground'));
	}
	return this;
};
/**
 * parametre ile girilen nesneyi  siler
 * @param obj
 */
function close(obj) {
	obj.fadeOut('slow', function () {
		jQuery(this).detach();
	});
};

var duyuruWindow = jQuery(document.body).Window('.window', true);
var denemeWindow = jQuery(document.body).Window('<div class="alert alert-info"><p>Bu duyurunun bir daha gösterilmesini istemiyorsanız bir daha gösterme butonuna basınız.</p></div>', false);

jQuery(document).ready(function () {
	//adminbar yüksekiliği notice container e aktarılıyor
	jQuery('.noticeContainer').css({'top': jQuery('#wpadminbar').height()});


	jQuery('.bar .close').click(function () {
		close(jQuery(this).parent())
	});

});

jQuery(window).resize(function () {
	duyuruWindow.reLocate();
});