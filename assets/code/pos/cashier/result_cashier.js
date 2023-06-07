$(document).ready(function(){    
    setInterval(clock, 1000);
    
    $('#product_table').dataTable({
        scrollY: '20vh',
        scrollCollapse: true,
    });    
});

function print_bill(pos_id) {
    window.open("pos/cashier/print_bill/"+pos_id, "Print Nota", "left=300, top=100, width=800, height=500");
}

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


