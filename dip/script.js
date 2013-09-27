$(document).foundation();

$(document).ready(function() {

  //Sort random function
  function random(owlSelector) {
    owlSelector.children().sort(function() {
      return Math.round(Math.random()) - 0.5;
    }).each(function() {
      $(this).appendTo(owlSelector);
    });
  }

  $("#customers ul").owlCarousel({
    navigation : true,
    navigationText: false,
    items : 8, //10 items above 1000px browser width
    itemsDesktop : [1000,7], //5 items between 1000px and 901px
    itemsDesktopSmall : [900,6], // betweem 900px and 601px
    itemsTablet: [600,4], //2 items between 600 and 0
    itemsMobile : [400,3],
    beforeInit : function(elem) {
      //Parameter elem pointing to $("#owl-demo")
      random(elem);
    }
  });

});

