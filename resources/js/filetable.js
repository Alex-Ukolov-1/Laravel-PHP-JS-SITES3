var FileTable = function(elementOrSelector, data) {
    if(window.location.href.split('/')[window.location.href.split('/').length - 1] !== 'edit'
        && window.location.href.split('/')[window.location.href.split('/').length - 1] !== 'create'
    ){
        return false;
    }
    let file_table =
        "<table id='file_table'>" +
            "<tr>" +
                "<th>Тип документа</th>" +
                "<th>Номер документа</th>" +
                "<th>Документ</th>" +
                "<th>Комментарий</th>" +
                "<th><a style='cursor: pointer;' class='btn btn-success btn-sm add_row'>Добавить</a></th>" +
            "</tr>";

    if (typeof data !== 'undefined' && data.length !== 0) {
        if (typeof data === 'string') data = JSON.parse(data);
        var key;
        let trip_id = data[0].trip_id;
        for (key in data) {
            let row = "<tr>";
            row+="<td>" +
                    "<select class='file_table_document_type' value='"+data[key].document_type_id+"' name='trip_files["+key+"][document_type]'>" +
                       getDocumentTypeOptions(data[key].document_type_id)
                    + "</select>" +
                    "</td>" +
                    "<td><input class='file_table_number' type='text' name='trip_files["+key+"][file_number]' value='"+data[key].trip_document_number+"'></td>" +
                    "<td>" +
                        "<input type='file' name='trip_files["+key+"][file]' value='"+data[key].document_path+"'>" +
                        "<a target='_blank' href='/uploads/"+data[key].document_path+"'>"+data[key].document_path+"</a>" +
                    "</td>" +
                    "<td><input type='text' name='trip_files["+key+"][file_comment]' value='"+data[key].document_comment+"'></td>";
            row+="</tr>";
            file_table+= row;
        }
        file_table+= "</table>";
        file_table+= "<div id='wrap_download_docs_trip'>" +
                        "<a data-id='"+trip_id+"' id='download_docs_trip'>Скачать документы рейса</a>" +
                     "</div>";
    }else {
        let clearRow =
            "<tr>" +
                "<td>" +
                    "<select class='file_table_document_type' name='trip_files[1][document_type]' id=''>" +
                        getDocumentTypeOptions() +
                    "</select>" +
                "</td>" +
                "<td><input type='text' class='file_table_number' name='trip_files[1][file_number]' value='1'></td>" +
                "<td><input type='file' name='trip_files[1][file]'></td>" +
                "<td><input type='text' name='trip_files[1][file_comment]'></td>" +
                "<td></td>" +
            "</tr>";
        file_table+= clearRow;
        file_table+= "</table>";
    }

    function getDocumentTypeOptions(document_type_id = ""){
        let options = "";
        let document_types = {
            1: 'Товарно-транспортная накладная',
            2: 'Транспортная накладная',
            3: 'Товарная накладная',
            4: 'Акт оказания услуг',
            5: 'Другой',
        };
        if(document_type_id === ""){
            options+= "<option selected>Выберите тип документа</option>";
        }
            $.each(document_types, function (key, val) {
                if (document_type_id !== "" && key === document_type_id){
                    options+= "<option selected value='"+key+"'>"+val+"</option>";
                }else{
                    options+= "<option value='"+key+"'>"+val+"</option>";
                }
            });

        return options;
    }

    $(elementOrSelector).append(file_table);
}

export default FileTable;
