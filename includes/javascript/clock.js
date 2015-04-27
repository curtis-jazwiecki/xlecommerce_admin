$.fn.clockize = function(options){  
    var opts = $.extend({}, $.fn.clockize.defaults, options); 
    this.each(function(){  
        var date_html = $('<div id="date"></div>');
        //var time_html = $.parseHTML('<ul><li id="hour"></li><li id="point">:</li><li id="min"></li><li id="point" class="hidden-phone">:</li><li id="sec" class="hidden-phone"></li></ul>');        
        var time_html = $.parseHTML('<ul><li id="hour"></li><li id="point">:</li><li id="min"></li></ul>');
        if (opts.date) $(this).append(date_html);
        $(this).append(time_html);
        
        $(this).addClass('clock');
        var newDate = new Date();
        newDate.setDate(newDate.getDate());
        var date =  $(this).find('#date');
        var hour =  $(this).find('#hour');
        var min =  $(this).find('#min');
        var sec =  $(this).find('#sec');
        
        date.html(opts.dayNames[newDate.getDay()] + " " + newDate.getDate() + ' ' + opts.monthNames[newDate.getMonth()] + ' ' + newDate.getFullYear());

        setInterval( function() {
            var seconds = new Date().getSeconds();
            sec.html(( seconds < 10 ? "0" : "" ) + seconds);
            },1000);
	
        setInterval( function() {
            var minutes = new Date().getMinutes();
            min.html(( minutes < 10 ? "0" : "" ) + minutes);
            },1000);
	
        setInterval( function() {
            var hours = new Date().getHours();
            hour.html(( hours < 10 ? "0" : "" ) + hours);
            }, 1000);
    });    
};  

$.fn.clockize.defaults = {  
    date: true,
    monthNames: [ "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December" ], 
    dayNames: ["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"]

};