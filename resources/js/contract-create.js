$(document).ready(function (){

    let contract_name = "";

    function generateContractName(){
        let c = "";
        $.each(selected_name_for_contract_name, function(index, value){
            if($('select[name='+value+']').val() !== ""){
                c+= $('select[name='+value+'] option:selected').text() + "|";
            }
        });
        return c.slice(0, -1);
    }

    let selected_name_for_contract_name = [ "cargo_type_id", "departure_point_id", "destination_id", "customer_id" ];

    $.each(selected_name_for_contract_name, function(index, value){
        $('select[name='+value+']').change(function (){
            contract_name = generateContractName();
            $(this).closest('form').find('input[name="name"]').val();
            $(this).closest('form').find('input[name="name"]').val(contract_name);
        });
    });

    //Подстановка Учета НДС при Форме оплаты - Безналичными с НДС
    $('select[name="loading_payment_type_id"]').change(function(){
        if( $(this).val() == 4 ) {
            $('input#vat_in_cargo_expenses2').removeAttr('checked');
            $('input#vat_in_cargo_expenses1').attr('checked', 'checked');
        }else{
            $('input#vat_in_cargo_expenses1').removeAttr('checked');
            $('input#vat_in_cargo_expenses2').attr('checked', 'checked');
        }
    });

    $('select[name="unloading_payment_type_id"]').change(function(){
        if( $(this).val() == 4 ) {
            $('input#vat_in_income2').removeAttr('checked');
            $('input#vat_in_income1').attr('checked', 'checked');
        }else{
            $('input#vat_in_income1').removeAttr('checked');
            $('input#vat_in_income2').attr('checked', 'checked');
        }
    });
});
