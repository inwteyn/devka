var sswInstagramApplication = function($){
  $(document.body).on('click', '.ssw-instagram-link', function(){
    var $container = $('#ssw-instagram-modal .ssw-instagram-content');
    $container.find('.ssw-hide').hide();
    if($(this).data('video-url')){
      $container.find('.ssw-img-loading').hide();
      $container.find('.ssw-instagram-video-wrapper').show();
      $container.find('.ssw-instagram-video-wrapper').html('<video controls><source src="' + $(this).data('video-url') + '" type="video/mp4"></video>')
    }else{
      $container.find('.ssw-img-loading').show();
      $container.find('.ssw-instagram-image-wrapper').show();
      $container.find('.ssw-instagram-image-wrapper > img').attr('src', $(this).data('image-url')).load(function(){
        $(this).parent().find('.ssw-img-loading').hide();
        $(this).css('display', 'block');
      });
    }

    var $item;
    if($(this).closest('.ssw-fade-hover').length){
      $item = $(this).closest('.ssw-fade-hover');
    }
    else{
      $item = $(this).closest('.ssw-instagram-item');
    }

    $container.find('.ssw-instagram-owner-link').attr('href', $item.find('.ssw-item-by > a').attr('href'));
    $container.find('.ssw-instagram-owner-image').attr('src', $item.find('.ssw-item-by img').attr('src'));
    $container.find('.ssw-instagram-owner-link:not(.ssw-thumb)').html($item.find('.ssw-item-by .ssw-username').html());
    $container.find('.ssw-instagram-detail-title').html($(this).data('text'));
    $container.find('.ssw-instagram-detail-products').html('');
    $container.find('.ssw-instagram-like-count').html($item.data('like-count'));
    $container.find('.ssw-instagram-comment-count').html($item.data('comment-count'));

    var initializeProducts = function(products){
      $.each(products, function(index, product){
        var $productWrapper = $('<div>', { title: product.title, class: 'ssw-instagram-detail-product' });

        var $imageWrapper = $('<div>', { class: 'ssw-instagram-detail-product-image' });
        var $link = $('<a>', { href: '/products/' + product.handle, target: '_blank' });
        var $image = $('<img>', { src: sswGetProductImageUrl(product.images[0].src, 'large')} );
        $link.append($image);
        $imageWrapper.append($link);
        $productWrapper.append($imageWrapper);

        var $linkWrapper = $('<div>', { class: 'ssw-instagram-detail-product-link' });
        var $link = $('<a>', { href: '/products/' + product.handle, target: '_blank' });
        $link.html(product.title);
        $linkWrapper.append($link);
        $productWrapper.append($linkWrapper);

        $container.find('.ssw-instagram-detail-products').append($productWrapper);
      });
    }

    var image_id = $item.data('image-id');
    if(typeof sswInstagramProducts[image_id] !== 'undefined'){
      initializeProducts(sswInstagramProducts[image_id]);
    }else{
      $.getJSON(sswProxyUrl + '/lite2/service/index/getInstagramProducts', { 'image_id': image_id }, function(response){
        sswInstagramProducts[image_id] = response;
        initializeProducts(response);
      });
    }

    $('#ssw-instagram-modal').sswModal();
  });
}

if(typeof loadSswInstagram == 'undefined') {
  var loadSswInstagram= setInterval(function(){
    if (typeof sswJqLoaded != 'undefined' && sswJqLoaded) {
      clearInterval(loadSswInstagram);
      sswInstagramApplication(ssw);
    }
  }, 10);
}
