$(document).ready(function(){    
    setInterval(clock, 1000);
    
    $('#dp_table, #sales_table, #pos_table, #sales_return_table, #expense_table, #collect_table').dataTable();
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


