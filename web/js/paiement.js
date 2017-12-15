
$(".Button-animationWrapper-child--primary Button").click(function() {
    
document.getElementById('div_attente').style.display = 'block';

});

$(".Header-navClose").click(function() {
    
alert("youyou");
});



$('.stripe_checkout_app').load(function(){
    var elements = document.getElementsByClassName('stripe_checkout_app');
    var requiredElement = elements[0];
    
            var iframe = requiredElement.contents();
    
            iframe.find(".Header-navClose").click(function(){
                   alert("test");
            });
    });