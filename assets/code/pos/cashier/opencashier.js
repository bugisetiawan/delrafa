$(document).ready(function(){    
	setInterval(clock, 1000);

	$('#modal').keyup(function() {
        $(this).val(format_number($(this).val()));
    });
	    
});

function clock()
{
    var waktu = new Date();	
    function plus0(number)
    {
        number = number < 10 ? '0'+ number : number;
        return number;
    }
	document.getElementById("hour").innerHTML = plus0(waktu.getHours());
	document.getElementById("minute").innerHTML = plus0(waktu.getMinutes());
	document.getElementById("second").innerHTML = plus0(waktu.getSeconds());
}

function format_number(angka, prefix){
	var number_string = angka.replace(/[^.\d]/g, '').toString(),
	split   		= number_string.split('.'),
	sisa     		= split[0].length % 3,
	rupiah     		= split[0].substr(0, sisa),
	ribuan     		= split[0].substr(sisa).match(/\d{3}/gi);
	if(ribuan){
		separator = sisa ? ',' : '';
		rupiah += separator + ribuan.join(',');
	}	
	rupiah = split[1] != undefined ? rupiah + '.' + split[1] : rupiah;
	return prefix == undefined ? rupiah : (rupiah ? 'Rp. ' + rupiah : '');
}


