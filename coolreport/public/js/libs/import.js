$('#file-import').on('change', tryParse);
$('#encoding-select').on('change', tryParse);
$('.tryparse').on('click', tryParse);
$('#tables-select').on('change', get_columns);

function readSingleFile(evt, data) 
{
    //Retrieve the first (and only!) File from the FileList object
    console.log(evt);
    console.log(data);
    $('.header-checkbox').show();

    if(evt != null && evt.target.localName == 'input')
    {
        var f = evt.target.files[0]; 
    }
    else
    {
        var f = $('#file-import')[0].files[0]; 
    }

    if (f || data) {
        if (data){
            paint_import_table(data['columns'], data['rows'], 2, false);    
        }
        else{
            // Elimino el div de la tabla de importacion (contiene Assignment/Header/Table)
            $("#import-table").children().remove();
            var r = new FileReader();
            r.onload = function(e) { 
            var contents = e.target.result;
            var lines = contents.split("\n");

            // Genero el codigo base del div
            //var tabla = '<label class="control-label">Assignment<i class="icon-spinner icon-spin" style="display:none"></i></label>';

            //tabla += '<label class="checkbox"><input name="header" type="checkbox" id="import-header" value="true">First line is the header</label>';

            var tabla = '<table class="tableimport table table-striped table-bordered table-condensed bootstrap-datatable datatable vertical-middle"><thead><tr>';

            var cabeceras = lines[0].split(";");

            var headers = 0;

            //tabla += '<option value="address">Address</option><option value="latitude">Latitude</option><option value="longitude">Longitude</option>';
            
            // console.log(fields);
            for (i=0; i<cabeceras.length; i++)
            {
                tabla += '<th> <select id="column-select" name="column'+ i +'" class="form-control" data-rel="chosen" data-placeholder="Choose a Column..."><option></option>';
                
                for (j=0; j<fields.length; j++)
                {            
                    //console.log(fields[j]['label'].toLowerCase(),$.trim(cabeceras[i]).toLowerCase());
                    if(fields[j]['label'].toLowerCase() == $.trim(cabeceras[i]).toLowerCase())
                    {
                        tabla += '<option value="'+ fields[j]['id'] +'" selected>'+ fields[j]['label'] + ' ['+ fields[j]['type'] +']</option>';
                        headers++;
                    }
                    else
                    {
                        tabla += '<option value="'+ fields[j]['id'] +'">'+ fields[j]['label'] + ' ['+ fields[j]['type'] +']</option>';
                    }
                }

                tabla += '</select></th>';
            }

            // Compruebo que la mitad de los elementos de la primera fila coinciden con las cabeceras, si es asi, la primera fila es cabecera
            if (headers >= cabeceras.length/2)
            {
                tabla = tabla.replace('"import-header"', '"import-header" checked');
            }

            tabla += '</tr><tr>';
            for (i=0; i<cabeceras.length; i++)
            {
                tabla += '<th>' + cabeceras[i] + '</th>';
            }
            
            tabla += '</tr></thead><tbody>';

            var limit = (lines.length > 3) ? 3 : lines.length;
            for (i=1; i<limit; i++)
            {
                var elementos = lines[i].split(";");

                tabla += '<tr>';

                for (j=0; j<elementos.length; j++)
                {
                    tabla += '<td>'+ elementos[j] +'</td>';
                }

                tabla += '</tr>';
            }

            tabla += '</tbody></table>';

            $("#import-table").append(tabla);
          }

          console.log($("#encoding-select").val());
          r.readAsText(f, $("#encoding-select").val());
        }
    } else { 
      alert("Failed to load file");
    }
}

function paint_import_table(columns, rows, limit, csv)
{
    // Elimino el div de la tabla de importacion (contiene Assignment/Header/Table)
    $("#import-table").children().remove();

    if ( csv )
    {
        $('.header-checkbox').show();
    }

    // Genero el codigo base del div
    //var tabla = '<label class="control-label">Assignment<i class="icon-spinner icon-spin" style="display:none"></i></label>';

    //tabla += '<label class="checkbox"><input name="header" type="checkbox" id="import-header" value="true">First line is the header</label>';

    var table = '<table class="tableimport table table-striped table-bordered table-condensed bootstrap-datatable datatable vertical-middle"><thead><tr>';

    var header = 0;

    //table += '<option value="address">Address</option><option value="latitude">Latitude</option><option value="longitude">Longitude</option>';

    for (i=0; i<columns.length; i++)
    {
        table += '<th> <select id="column-select" name="column'+ i +'" class="form-control" data-rel="chosen" data-placeholder="Choose a Column..."><option></option>';
        for (j=0; j<fields.length; j++)
        {            
            //console.log(fields[j]['label'].toLowerCase(),$.trim(columns[i]).toLowerCase());
            if(fields[j]['label'].toLowerCase() == $.trim(columns[i]).toLowerCase())
            {
                table += '<option value="'+ fields[j]['id'] +'" selected>'+ fields[j]['label'] + ' ['+ fields[j]['type'] +']</option>';
                header++;
            }
            else
            {
                table += '<option value="'+ fields[j]['id'] +'">'+ fields[j]['label'] + ' ['+ fields[j]['type'] +']</option>';
            }
        }

        table += '</select></th>';
    }

    // Compruebo que la mitad de los elementos de la primera fila coinciden con las cabeceras, si es asi, la primera fila es cabecera
    if (header >= columns.length/2)
    {
        table = table.replace('"import-header"', '"import-header" checked');
    }

    table += '</tr><tr>';

    for (i=0; i<columns.length; i++)
    {
        table += '<th>' + columns[i] + '</th>';
    }

    table += '</tr></thead><tbody>';

    limit = (rows.length > limit) ? limit : rows.length;
    for (i=0; i<limit; i++)
    {
        table += '<tr>';

        var elements = rows[i];

        for (j=0; j<elements.length; j++)
        {
            table += '<td>'+ elements[j] +'</td>';
        }

        table += '</tr>';
    }

    table += '</tbody></table>';

    $("#import-table").append(table);
}    

function tryParse(e)
{
    e.preventDefault();
    $("#import-table").children().remove();

    if ( $('#data-select').val() == 'CSV' )
    {
        var csv = ($('.tab-pane.fade.active.in').attr('id') == 'file') ? $('#file-import')[0].files[0] : $('#pastearea').val();
        var columns;
        var rows;
        
        var parse = Papa.parse(csv, {
            delimiter: ";",
            header: false,
            dynamicTyping: false,
            preview: 0,
            step: undefined,
            encoding: $('#encoding-select').val(),
            worker: false,
            comments: false,
            complete: function(results, file) {
                columns = results.data[0];
                rows = results.data.slice(1);
                paint_import_table(columns, rows, 2, true);
            },
            error: undefined,
            download: ($('.tab-pane.fade.active.in').attr('id') == 'url') ? true : false, // Esto debe estar en true para casos de remote files
            keepEmptyRows: false,
            chunk: undefined,
        });

        if ( $('.tab-pane.fade.active.in').attr('id') == 'paste' && parse !== null )
        {
            columns = parse.data[0];
            rows = parse.data.slice(1); 
            paint_import_table(columns, rows, 2, true);
            $('#csv-paste').val(JSON.stringify(parse.data));
        }
         $('#csv-type').val($('.tab-pane.fade.active.in').attr('id'));
    }
    else
    {
        get_tables(e);
    }
}

function get_tables(e)
{
    e.preventDefault();
    $("#import-table").children().remove();

    var system = $('#dbsystem-select').val();
    var host = $('#host-db').val();
    var port = $('#port-db').val();
    var database = $('#database-db').val();
    var user = $('#user-db').val();
    var password = $('#password-db').val();

    var json = {system: system, host: host, port: port, database: database, user: user, password: password};

    $.post("dbtables", json).success(function(data){
        $("#tables-select")[0].options.length = 1;
        for(i=0; i<data.length; i++)
        {
            $("#tables-select").append(new Option(data[i], data[i]));
        }
        $('#tables-db').show();
    });
}

function get_columns(e)
{
    e.preventDefault();
    var system = $('#dbsystem-select').val();
    var host = $('#host-db').val();
    var port = $('#port-db').val();
    var database = $('#database-db').val();
    var user = $('#user-db').val();
    var password = $('#password-db').val();
    var table = $('#tables-select').val();

    var json = {system: system, host: host, port: port, database: database, user: user, password: password, table: table};

    $.post("dbcolumns", {system: system, host: host, port: port, database: database, user: user, password: password, table: table}).success(function(data){
         paint_import_table(data['columns'], data['rows'], 2, false);
    });
}

$('#dbsystem-select').on('change', function(evt){
    if ( evt.target.value == 'SQL Server' ) 
    {
        $('#host-db').prop("disabled", true);
        $('#host-db').val('');
        $('#port-db').prop("disabled", true);
        $('#port-db').val('');
    }
    else
    {
        $('#host-db').prop("disabled", false);
        $('#port-db').prop("disabled", false);
    }
});

$('#data-select').on('change', function(evt){
    if ( $('#template-select').val() != '' )
    {
        show_type_block();
    }
});

function show_type_block()
{
    $("#import-table").children().remove();
    if ( $('#data-select').val() == 'Base de datos' ) 
    {
        $('.file-group').hide();
        $('#file-import').prop('required', false); 
        $('.webservice-group').hide();
        $('.db-group').show();
        $('#host-db').prop('required',true);     
        $('#port-db').prop('required',true);     
        $('#database-db').prop('required',true);     
        $('#user-db').prop('required',true);     
        $('#password-db').prop('required',true);     
    }
    else if ( $('#data-select').val() == 'Servicio web' )
    {
        $('.file-group').hide();
        $('#file-import').prop('required',false); 
        $('.db-group').hide();
        $('#tables-db').hide();
        $('#host-db').prop('required',false);     
        $('#port-db').prop('required',false);     
        $('#database-db').prop('required',false);     
        $('#user-db').prop('required',false);     
        $('#password-db').prop('required',false);  
        $('.webservice-group').show();
    }
    else
    {
        $('.db-group').hide();
        $('#tables-db').hide();
        $('#host-db').prop('required',false);     
        $('#port-db').prop('required',false);     
        $('#database-db').prop('required',false);     
        $('#user-db').prop('required',false);     
        $('#password-db').prop('required',false);  
        $('.webservice-group').hide();
        $('.file-group').show();
        //$('#file-import').prop('required',true);
        $('#file-import').prop('required', false); 
        $('.header-checkbox').hide();
    }
}