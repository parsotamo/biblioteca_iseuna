// Simple-DataTables
// https://github.com/fiduswriter/Simple-DataTables/wiki


window.addEventListener('DOMContentLoaded', _ => {

	var datatablesSimple = document.getElementById('datatablesSimple');

	if(datatablesSimple)
	{
		new simpleDatatables.DataTable(datatablesSimple, {
			"oLanguage": {
				"sLengthMenu": "Mostar _MENU_ records per page",
				"sZeroRecords": "Nada encontrado",
				"sInfo": "Mostrando _START_ to _END_ de _TOTAL_ registos",
				"sInfoEmpty": "Monstrando 0 to 0 of 0 registo",
				"sInfoFiltered": "(filtered from _MAX_ total records)"
			}
		});
	}

});