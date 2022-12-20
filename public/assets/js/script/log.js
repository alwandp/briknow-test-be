new TypeIt('#title', {
    strings: "Find Your Project here",
    speed: 150
  })
  .move(-3, {delay: 200})
  .delete(1, {delay: 600})
  .type('H')
  .move('END')
  .type("!")
  .go();

let waiting = `
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" style="background: none; display: inline; shape-rendering: auto;" width="150px" height="70px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid">
                    <rect x="17.5" y="30" width="15" height="40" fill="#9efffb">
                    <animate attributeName="y" repeatCount="indefinite" dur="0.7575757575757576s" calcMode="spline" keyTimes="0;0.5;1" values="18;30;30" keySplines="0 0.5 0.5 1;0 0.5 0.5 1" begin="-0.15151515151515152s"></animate>
                    <animate attributeName="height" repeatCount="indefinite" dur="0.7575757575757576s" calcMode="spline" keyTimes="0;0.5;1" values="64;40;40" keySplines="0 0.5 0.5 1;0 0.5 0.5 1" begin="-0.15151515151515152s"></animate>
                    </rect>
                    <rect x="42.5" y="30" width="15" height="40" fill="#6ae2f8">
                    <animate attributeName="y" repeatCount="indefinite" dur="0.7575757575757576s" calcMode="spline" keyTimes="0;0.5;1" values="20.999999999999996;30;30" keySplines="0 0.5 0.5 1;0 0.5 0.5 1" begin="-0.07575757575757576s"></animate>
                    <animate attributeName="height" repeatCount="indefinite" dur="0.7575757575757576s" calcMode="spline" keyTimes="0;0.5;1" values="58.00000000000001;40;40" keySplines="0 0.5 0.5 1;0 0.5 0.5 1" begin="-0.07575757575757576s"></animate>
                    </rect>
                    <rect x="67.5" y="30" width="15" height="40" fill="#9efffb">
                    <animate attributeName="y" repeatCount="indefinite" dur="0.7575757575757576s" calcMode="spline" keyTimes="0;0.5;1" values="20.999999999999996;30;30" keySplines="0 0.5 0.5 1;0 0.5 0.5 1"></animate>
                    <animate attributeName="height" repeatCount="indefinite" dur="0.7575757575757576s" calcMode="spline" keyTimes="0;0.5;1" values="58.00000000000001;40;40" keySplines="0 0.5 0.5 1;0 0.5 0.5 1"></animate>
                    </rect>
                    </svg>
                `;
$('#refresh').click(function(){
    $.ajax({
        type:'GET',
        url:'refreshcaptcha',
        beforeSend: function()
        {
            $(".captcha span").html(waiting);
        },
        success:function(data){
            $(".captcha span").html(data.captcha);
        }
    });
});

function deleteCookies() { 
    var allCookies = document.cookie.split(';'); 
    
    // The "expire" attribute of every cookie is  
    // Set to "Thu, 01 Jan 1970 00:00:00 GMT" 
    for (var i = 0; i < allCookies.length; i++) 
        document.cookie = allCookies[i] + "=;expires=" 
        + new Date(0).toUTCString(); 
} 