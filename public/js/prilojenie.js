$(document).ready(function(){
    var combo_student=$('#combo_student');
    var menu_student=$('#menu_student');
    var combo_profession=$('#combo_profession');
    var combo_study_form=$('#combo_study_form');
    var combo_year=$('#combo_year');
    var list_professions=$('#list_professions');
    var list_study_form=$('#list_study_form');
    var list_year=$('#list_year');
    var temp_button=$('#temp_button');
    // var table_transcript=$('#table-transcript');
    var body=$('body');
    var submit=$('#submit');
    var error_message=$('#error_message');
    var form_prilojenie=$('#form_prilojenie');


    temp_button.on('click',function () {
        var id_profession = combo_profession.dropdown('get value');
        var id_study_form = combo_study_form.dropdown('get value');
        var id_year = combo_year.dropdown('get value');
        submit.addClass('disabled');
		combo_student.addClass('loading');
        if ((id_profession>0) && (id_study_form>0) && (id_year>0)) {
            $.get("/students/" + id_profession + "/" + id_study_form + "/" + id_year, function (data) {

                data = JSON.parse(data);
                var html = "";
                for (var i = 0; i < data.length; i++) {
                    html = html + "<div class='item' data-value='" + data[i].id + "' data-status='" + data[i].isStudent + "'>" + data[i].fio + "</div>";
                }
                menu_student.html(html);

            }).always(function(){
            combo_student.removeClass('loading');
        });
        }
    });

    submit.on('click', function(){
        // var checked = $(".check-praktice.checked");
        // var array_praktika =[];
        // checked.each(function (index){
        //    // alert($(this).html());
        //    var transcript_id = $(this).parent().parent().attr('data-id');
        //    var praktika_id = $(this).parent().parent().find('.tip-praktice').dropdown('get value');
        //    array_praktika.push({'id': parseInt(transcript_id),'type':parseInt(praktika_id)});
        //
        // });
        //alert(array_praktika.length);

        //alert('{"ap":'+JSON.stringify(array_praktika)+'}');
        // if (array_praktika.length>0){
        var studentid=combo_student.dropdown('get value');
        var isStudent=menu_student.find(".item[data-value='"+studentid+"']").attr('data-status');
        //alert(studentid);
        var form_data= new FormData();
        form_data.append('studentid',studentid);
        // form_data.append('array_praktika','{"ap":'+JSON.stringify(array_praktika)+'}');
        form_data.append('isstudent',isStudent);
        form_data.append('_token',$("input[name='_token']").val());
		combo_student.addClass('loading');
        $.ajax({
            url: "/prilojenie_excel",
            type: 'POST',
            data: form_data,
            async: false,
            processData: false,
            contentType: false,
            cache: false,
            success:function (data){
                try{
                    data=JSON.parse(data);
                    //error_message.html(data.message).addClass('visible').removeClass('hidden');
                    //window.location='/prilojenie/'+data.file_name;
					if (data.message=='uspewno'){
                        window.location=data.file_name ;
                    }else
                    {
                        error_message.html(data.message).addClass('visible').removeClass('hidden');
                    }
                }catch ($e){
                }
            }

        }).always(function(){
            combo_student.removeClass('loading');
        });
        // }else {

        // }

    });

    combo_profession.dropdown({
        onChange:function(val){
            if (isEmpty(val)==false) {
                combo_student.dropdown('clear');
                combo_study_form.dropdown('clear');
                combo_year.dropdown('clear');
                submit.addClass('disabled');
                list_year.empty();
                menu_student.empty();
                list_study_form.empty();
                submit.addClass('disabled');
				combo_study_form.addClass('loading');
                $.get("/contingent/profession/"+val, function(data){
                    data = JSON.parse(data);
                    var html = "<option class='default_value' value=''></option>";
                    for(var i = 0; i < data.length; i++){
                        html = html + '<option class="item_value" value="'+data[i].id+'">'+data[i].name+'</option>';
                    }
                    $('#id_study_form').html(html);
                }).always(function(){
                combo_study_form.removeClass('loading');
            });
            }
        }
    });

    combo_study_form.dropdown({
        onChange:function (val) {
            if (isEmpty(val)==false) {
                var id_profession = combo_profession.dropdown('get value');
                combo_student.dropdown('clear');
                combo_year.dropdown('clear');
                list_year.empty();
                menu_student.empty();
                submit.addClass('disabled');
				combo_year.addClass('loading');
                $.get("/list_year/" + id_profession + "/" + val, function (data) {

                    data = JSON.parse(data);

                    for (var i = 0; i < data.length; i++) {
                        if (data[i].FinishDate == 'не указан год') {
                            list_year.append("<div class='item' data-value='9999'>" + data[i].FinishDate + "</div>");
                        } else {
                            list_year.append("<div class='item' data-value='" + data[i].FinishDate + "'>" + data[i].FinishDate + "</div>");
                        }

                    }


                }).always(function () {
                    combo_year.removeClass('loading');
                });
            }
        }
    });
    combo_year.dropdown({
            onChange: function (val) {
                if (isEmpty(val)==false) {
                    combo_student.dropdown('clear');
                    menu_student.empty();
                    $("#submit").addClass('disabled');
                }
            }
        }
    );


    combo_student.dropdown({
        onChange:function (val){
            $("#submit").removeClass('disabled');
            //         error_message.html('').addClass('hidden').removeClass('visible');
            // if (isEmpty(val)==false) {
            //    var isStudent=menu_student.find(".item[data-value='"+val+"']").attr('data-status');
            //         table_transcript.empty();
            //         $.get("/student_transcript/"+val+"/"+isStudent, function(data) {
            //             try {
            //                 data = JSON.parse(data);
            //
            //                 for (var i = 0; i < data.length; i++) {
            //                     var row=document.createElement('tr');
            //                         row.setAttribute('data-id',data[i].id);
            //                         row.setAttribute('data-id',data[i].id);
            //                     var td_check=document.createElement('td');
            //                     td_check.className='collapsing';
            //                     td_check.innerHTML="<div class='ui fitted slider checkbox check-praktice'><input type='checkbox'><label></label></div>";
            //                     var td_tip=document.createElement('td');
            //                     td_tip.innerHTML="<div class='ui selection dropdown tip-praktice hidden' data-id='"+data[i].id+"' style='width: 100%'>"+
            //                                         "<input type='hidden' name='praktice'>"+
            //                                         "<i class='dropdown icon'></i>"+
            //                                         "<div class='default text'>Выберите тип практики</div>"+
            //                                         "<div class='menu'>"+
            //                                         "<div class='item' data-value='1'>Производственная практика</div>"+
            //                                         "<div class='item' data-value='2'>Учебная практика</div>"+
            //                                         "<div class='item' data-value='3'>Педагогическая практика</div>"+
            //                                         "<div class='item' data-value='4'>Технологическая практика</div>"+
            //                                         "<div class='item' data-value='5'>Профессиональная практика</div>"+
            //                                         "</div>"+
            //                                         "</div>";
            //                     var td_dis=document.createElement('td');
            //                     td_dis.innerHTML=data[i].subjectnameRU;
            //                     row.appendChild(td_check);
            //                     row.appendChild(td_tip);
            //                     row.appendChild(td_dis);
            //                     table_transcript.append(row);
            //                 }
            //                 $(".tip-praktice").dropdown();
            //                 activatecheckbox();
            //
            //                 if (data.length>0){
            //                     $("#submit").prop({
            //                         disabled: false
            //                     });
            //                 }
            //                 //alert('asdagag');
            //             } catch (e){
            //                 error_message.html(data).addClass('visible').removeClass('hidden');
            //             }
            //         });
            //         }
        }
    });

    // function activatecheckbox(){
    //     $(".check-praktice").checkbox({
    //       onChecked:function () {
    //           //alert('checked');
    //           //alert($(this).parent().parent().parent().html());
    //           //alert($(this).parent().parent().parent().attr('data-id'));
    //           $(this).parent().parent().parent().find('.tip-praktice').removeClass('hidden');
    //       },
    //       onUnchecked:function(){
    //           //alert('unchecked');
    //           $(this).parent().parent().parent().find('.tip-praktice').addClass('hidden');
    //     }
    //     });
    // }


    $('.form').form({
        fields: {
            id_profession: {
                identifier: 'id_profession',
                rules: [
                    {
                        type: 'empty',
                        prompt: 'Please select a dropdown value'
                    }
                ]
            }
        }
    });
});

function isEmpty(obj) {

    if (obj == null) return true;

    if (obj.length > 0)    return false;
    if (obj.length === 0)  return true;

    if (typeof obj !== "object") return true;

    return true;
}