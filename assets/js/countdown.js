jQuery(function($) {

  $('.tip_product_countdown_container').each(function() {
    var $container = $(this);
    var end = parseInt($container.attr('data-end'), 10) * 1000;

    function update() {
      var now  = Date.now();
      var diff = end - now;

      if (diff <= 0) {
        $container.text(tip_product_countdown_data.expired_countdown_text);
        clearInterval(timer);
        return;
      }

      var days    = Math.floor(diff / (1000 * 60 * 60 * 24));
      var hours   = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
      var minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
      var seconds = Math.floor((diff % (1000 * 60)) / 1000);

      $container.find('.tip_days').text(days);
      $container.find('.tip_hours').text(hours);
      $container.find('.tip_minutes').text(minutes);
      $container.find('.tip_seconds').text(seconds);
    }

    update();
    var timer = setInterval(update, 1000);

  });

});