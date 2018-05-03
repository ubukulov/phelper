
@extends('layouts/kadr')
@section('content')
    <div class="row" style="margin-top: 80px; margin-left: 270px;">
        <h1>Список отчетов</h1>
        <hr>
        <ol>
            <li><a href="{{ url('/kadr/report/1') }}">Список докторов наук.</a></li>
            <li><a href="{{ url('/kadr/report/2') }}">Список докторов PhD.</a></li>
			<li><a href="{{ url('/kadr/report/3') }}">Список пенсионеров.</a></li>
			<li><a href="{{ url('/kadr/report/4') }}">Список кандидатов наук. </a></li>
			<li><a href="{{ url('/kadr/report/5') }}">Список магистрантов. </a></li>
			<li><a href="{{ url('/kadr/report/6') }}">Список уволенных по университету. </a></li>
			<li><a href="{{ url('/kadr/report/7') }}">Список Совместителей по университету. </a></li>
			<li><a href="{{ url('/kadr/report/8') }}"> Список принятых по университету.</a></li>
			<li><a href="{{ url('/kadr/report/9') }}"> Список юбиляров 55,60,65,70,75,80 лет по университету.</a></li>
			<li><a href="{{ url('/kadr/report/10') }}"> Список штатных женщин по университету.</a></li>
			<li><a href="{{ url('/kadr/report/11') }}"> Количественный и качественный состав(Кафедра Педагогика).</a></li>
					<li><a href="{{ url('/kadr/report/12') }}"> Количественный и качественный состав(Институт).</a></li>
					<li><a href="{{ url('/kadr/report/13') }}"> Количественный и качественный состав(Кафедра политология и социально-философские единицины).</a></li>
					<li><a href="{{ url('/kadr/report/14') }}"> Количественный и качественный состав(Университет).</a></li>
					<li><a href="{{ url('/kadr/report/15') }}"> Количественный и качественный состав(Кафедра Оздровительной физической культуры).</a></li>
					<li><a href="{{ url('/kadr/report/16') }}"> Количественный и качественный состав(Кафедра Военная).</a></li>
					<li><a href="{{ url('/kadr/report/17') }}"> Количественный и качественный состав(Кафедра Казахского языка).</a></li>
					<li><a href="{{ url('/kadr/report/18') }}"> Количественный и качественный состав(Кафедра Русского языка и литературы).</a></li>
					<li><a href="{{ url('/kadr/report/20') }}"> Количественный и качественный состав(Кафедра Преподавания филологических дициплин).</a></li>
					<li><a href="{{ url('/kadr/report/21') }}"> Количественный и качественный состав(Кафедра Общего языкознания).</a></li>
					<li><a href="{{ url('/kadr/report/22') }}"> Количественный и качественный состав(Кафедра Профессиональной иноязычной коммуникации).</a></li>
					<li><a href="{{ url('/kadr/report/23') }}"> Количественный и качественный состав(Кафедра Профессионально-ориентированных языков).</a></li>
					<li><a href="{{ url('/kadr/report/24') }}"> Количественный и качественный состав(Кафедра Восточных языков и перевода).</a></li>
					<li><a href="{{ url('/kadr/report/25') }}"> Количественный и качественный состав(Институт Филологии полиязычного образования).</a></li>
					<li><a href="{{ url('/kadr/report/26') }}"> Количественный и качественный состав(Кафедра Оздровительной физической культуры).</a></li>
					<li><a href="{{ url('/kadr/report/27') }}"> Количественный и качественный состав(Кафедра Оздровительной физической культуры).</a></li>
					<li><a href="{{ url('/kadr/report/28') }}"> Количественный и качественный состав(Кафедра Оздровительной физической культуры).</a></li>
					<li><a href="{{ url('/kadr/report/29') }}"> Количественный и качественный состав(Кафедра Оздровительной физической культуры).</a></li>
					<li><a href="{{ url('/kadr/report/30') }}"> Количественный и качественный состав(Кафедра Оздровительной физической культуры).</a></li>
					<li><a href="{{ url('/kadr/report/31') }}"> Количественный и качественный состав(Кафедра Оздровительной физической культуры).</a></li>
					
					</ol>
    </div>

	<div class='row' style=' margin-left: 270px;'>
		<div class='col-md-6'>
			<div class='form-group'>
				<label>Выберите преподавателя</label>
				<select class='form-control'>
					<option></option>
					<option></option>
					<option></option>
					<option></option>
				</select>
			</div>
			<tr>
                                                            <td width=20% nowrap>
                                                                Место рождения(если родился в другой стране)
                                                            </td>
                                                            <td>
                                                                <input style='width:100%' class='commonInput' type='text'
                                                                       name='city'
                                                                       value="Алматы"
                                                                       maxlength="2048" placeholder="Например КНР, Узбекистан, Россия"/>
                                                            </td>
                                                        </tr>
		
		<div class='col-md-6'>
			<div class='form-group'>
				<label>Составу по перечню сотрудников</label>
				<select class='form-control'>
					<option>АУП</option>
					<option>МОП</option>
					<option>ППС</option>
					<option>УВП</option>
				</select>
			
			
			<tr>
					<td align="right">
						<table border=0 width="100%">
							<tr>
								<td width="20%" class="tdClass">
									Выберите Институт
								</td>
								<td width="80%">
									<SELECT class="select"  style="width:100%;"  name="facultyID"  onChange="null; form.submit()"  id="facultyID" >
<OPTION class="option" VALUE="0"  selected >Все факультеты</OPTION>
<OPTION class="option" VALUE="1" >Институт естествознания и географии</OPTION>
<OPTION class="option" VALUE="2" >Институт искусств, культуры и спорта</OPTION>
<OPTION class="option" VALUE="7" >Институт истории и права</OPTION>
<OPTION class="option" VALUE="3" >Институт математики, физики и информатики</OPTION>
<OPTION class="option" VALUE="4" >Институт педагогики и психологии</OPTION>
<OPTION class="option" VALUE="14" >Институт Сорбонна-Казахстан</OPTION>
<OPTION class="option" VALUE="13" >Институт филологии и полиязычного образования</OPTION>
<OPTION class="option" VALUE="12" >Общеуниверситетская  кафедра</OPTION>
<OPTION class="option" VALUE="15" >Факультет для иностранных граждан и довузовской подготовки (Foundation)</OPTION>
</SELECT>
								</td>
							</tr>
							<tr>
								<td class="tdClass">
									Выберите кафедру
								</td>
								<td>
									<SELECT class="select"  style="width:100%;"  name="cafedraID"  onChange="null; form.submit()"  id="cafedraID" ><OPTION class="option" VALUE="0"  selected >Все кафедры</OPTION>
<OPTION class="option" VALUE="55" >Военная кафедра</OPTION>
<OPTION class="option" VALUE="53" >Кафедра     преподавания филологических дисциплин</OPTION>
<OPTION class="option" VALUE="5" >Кафедра анатомии, физиологии, зоологии и безопастности жизнедеятельности</OPTION>
<OPTION class="option" VALUE="6" >Кафедра ботаники и общей биологии</OPTION>
<OPTION class="option" VALUE="35" >Кафедра восточных языков и перевода</OPTION>
<OPTION class="option" VALUE="45" >Кафедра всемирной истории</OPTION>
<OPTION class="option" VALUE="2" >Кафедра географии Казахстана и экологии</OPTION>
<OPTION class="option" VALUE="29" >Кафедра государственных гражданско-правовых дисциплин</OPTION>
<OPTION class="option" VALUE="21" >Кафедра дошкольного образования и социальной педагогики</OPTION>
<OPTION class="option" VALUE="46" >Кафедра информатики  и  информатизации  образования</OPTION>
<OPTION class="option" VALUE="25" >Кафедра информатики и информационных технологий</OPTION>
<OPTION class="option" VALUE="44" >Кафедра истории Казахстана им.Академика Т.С. Садыкова</OPTION>
<OPTION class="option" VALUE="33" >Кафедра казахского языка</OPTION>
<OPTION class="option" VALUE="15" >Кафедра математики и математического моделирования</OPTION>
<OPTION class="option" VALUE="40" >Кафедра международного права</OPTION>
<OPTION class="option" VALUE="39" >Кафедра международных отношений</OPTION>
<OPTION class="option" VALUE="43" >Кафедра методики преподавания истории и общественных дисциплин</OPTION>
<OPTION class="option" VALUE="57" >Кафедра методики преподавания математики, физики и информатики</OPTION>
<OPTION class="option" VALUE="12" >Кафедра музыкального образования и хореографии</OPTION>
<OPTION class="option" VALUE="62" >Кафедра общего  языкознания </OPTION>
<OPTION class="option" VALUE="18" >Кафедра общей и прикладной психологии</OPTION>
<OPTION class="option" VALUE="56" >Кафедра оздоровительной физической культуры</OPTION>
<OPTION class="option" VALUE="20" >Кафедра педагогика и методики начального обучения</OPTION>
<OPTION class="option" VALUE="22" >Кафедра педагогики</OPTION>
<OPTION class="option" VALUE="49" >Кафедра педагогики и психологии</OPTION>
<OPTION class="option" VALUE="14" >Кафедра педагогов организаторов и начальной военной подготовки</OPTION>
<OPTION class="option" VALUE="50" >Кафедра политологии и социально-философских дисциплин</OPTION>
<OPTION class="option" VALUE="66" >Кафедра профессионально-ориентированных языков</OPTION>
<OPTION class="option" VALUE="38" >Кафедра профессиональной иноязычной коммуникации</OPTION>
<OPTION class="option" VALUE="37" >Кафедра русского языка и литературы</OPTION>
<OPTION class="option" VALUE="19" >Кафедра специального образования</OPTION>
<OPTION class="option" VALUE="7" >Кафедра страноведения и туризма</OPTION>
<OPTION class="option" VALUE="10" >Кафедра творческих специальностей</OPTION>
<OPTION class="option" VALUE="11" >Кафедра теории и методики изобразительного и декоративного искусства</OPTION>
<OPTION class="option" VALUE="60" >Кафедра теории и методики подготовки педагогов физической культуры и спорта</OPTION>
<OPTION class="option" VALUE="3" >Кафедра технологии обучения естественных дисциплин</OPTION>
<OPTION class="option" VALUE="28" >Кафедра уголовно-правовых дисциплин</OPTION>
<OPTION class="option" VALUE="17" >Кафедра физики</OPTION>
<OPTION class="option" VALUE="32" >Кафедра филологических специальностей для иностранных граждан и довузовской подготовки</OPTION>
<OPTION class="option" VALUE="1" >Кафедра химии</OPTION>
<OPTION class="option" VALUE="65" >Кафедра экономических специальностей</OPTION>
</SELECT>
<tr>
                <td class="tdClass" width="25%">
                    Вид награды
                </td>
                <td class="tdClass" width="25%">
                    <SELECT class="select"   onChange=''  style="width:100%;" name=""  id="" ><OPTION class="option" VALUE="1" >Гасударственная награда</OPTION><OPTION class="option" VALUE="2" >Ведомственная награда(МОН РК)</OPTION><OPTION class="option" VALUE="3"  selected >Университетская награда</OPTION></SELECT>
                </td>
				
            </tr>
			<tr>
    <td class="tdClass">
        Академик (название академии)
    <td>
        <input type=text name="timetable_description" value="" class="commonInput"
               style="width:100%">
    </td>
</tr>
<tr>
                <td class="tdClass" width="25%">
                    Дата
                </td>
                <td class="tdClass" width="25%">
                    <table border=0 cellpadding=0 cellspacing=0><tr><td valign="middle">
<INPUT class="commonInput" name="bd" id="tbd"  style="width:80px"  value="12-12-2014" ></td><td valign="middle"><input name="bdSelect"  style="width:25px; height:20px" type="button" id="anchorbd"  name="anchorbd"  onclick="try { cal.select(document.form.bd, 'anchorbd','dd-MM-yyyy'); return false;} catch(e){alert(e);}"  value="..." /></td></tr></table>
</tr>
<tr>
    <td class="tdClass">
        Член-корр.(название Академии)
    <td>
        <input type=text name="timetable_description" value="" class="commonInput"
               style="width:100%">
    </td>
</tr>
<tr>
                <td class="tdClass" width="25%">
                    Дата
                </td>
                <td class="tdClass" width="25%">
                    <table border=0 cellpadding=0 cellspacing=0><tr><td valign="middle">
<INPUT class="commonInput" name="bd" id="tbd"  style="width:80px"  value="12-12-2014" ></td><td valign="middle"><input name="bdSelect"  style="width:25px; height:20px" type="button" id="anchorbd"  name="anchorbd"  onclick="try { cal.select(document.form.bd, 'anchorbd','dd-MM-yyyy'); return false;} catch(e){alert(e);}"  value="..." /></td></tr></table>
</tr>
								</td>
							</tr>
			<tr>
    <td>
        <table border=0 width="100%">
            <tr>
                <td class="tdClass" width=20%>
                    Дата начала работы (в ВУЗе)
                </td>
                <td>
                    <table border=0 cellpadding=0 cellspacing=0><tr><td valign="middle">
<INPUT class="commonInput" name="sd" id="tsd"  style="width:80px"  value="01-09-1992" ></td><td valign="middle"><input name="sdSelect"  style="width:25px; height:20px" type="button" id="anchorsd"  name="anchorsd"  onclick="try { cal.select(document.form.sd, 'anchorsd','dd-MM-yyyy'); return false;} catch(e){alert(e);}"  value="..." /></td></tr></table>
                </td>
           
			</tr>
            <!--Nps perenesen na tutor fields -->
            <tr>
                <td class="tdClass" width=20%>
                    Дата начала рабочего стажа
                </td>
                <td>
                    <table border=0 cellpadding=0 cellspacing=0><tr><td valign="middle">
<INPUT class="commonInput" name="wsd" id="twsd"  style="width:80px"  value="01-09-1992" ></td><td valign="middle"><input name="wsdSelect"  style="width:25px; height:20px" type="button" id="anchorwsd"  name="anchorwsd"  onclick="try { cal.select(document.form.wsd, 'anchorwsd','dd-MM-yyyy'); return false;} catch(e){alert(e);}"  value="..." /></td></tr></table>
                </td>
            </tr>
			
            <tr>
                <td class="tdClass" width=20%>
                    Стаж работы
                </td>
                
				<td class="tdClass">
                    В ВУЗе 24,8, НПС 24,8 (Общий 24,8)
                </td>
            </tr>
			 <tr>
                <td class="tdClass" width=20% nowrap>
                    Должность
                </td>
                <td width="80%">
                    <input style="width:100%" class="commonInput" name="ad" value=""
                           maxlength="256"/>
                </td>
            </tr>
				 <tr>
                <td class="tdClass" width=20% nowrap>
                    №приказа
                </td>
                <td width="80%">
                    <input style="width:100%" class="commonInput" name="ad" value=""
                           maxlength="256"/>
                </td>
            </tr>
			<tr>
                <td class="tdClass" width="10%">
                    Дата приказа
                </td>
                <td width="10%">
                    <table border=0 cellpadding=0 cellspacing=0><tr><td valign="middle">
<INPUT class="commonInput" name="icdt" id="ticdt"  style="width:80px"  value="" ></td><td valign="middle"><input name="icdtSelect"  style="width:25px; height:20px" type="button" id="anchoricdt"  name="anchoricdt"  onclick="try { cal.select(document.form.icdt, 'anchoricdt','dd-MM-yyyy'); return false;} catch(e){alert(e);}"  value="..." /></td></tr></table>
                </td>
            </tr>
			<tr>
			<td class="tdClass" width=20% nowrap>
                    Испытательный срок
                </td>
                <td width="80%">
                    <input style="width:100%" class="commonInput" name="ad" value=""
                           maxlength="256"/>
                </td>
            </tr>
			  <tr>
                <td class="tdClass" width="25%">
                    Срок на какой месяц
                </td>
                <td class="tdClass" width="25%">
                    <SELECT class="select"   onChange=''  style="width:100%;" name="ws"  id="ws" ><OPTION class="option" VALUE="1" >1 месяц</OPTION><OPTION class="option" VALUE="2" >2 месяц</OPTION><OPTION class="option" VALUE="3"  selected >3 месяц</OPTION></SELECT>
                </td>
				
            </tr>
			<tr>
                <td class="tdClass" width="25%">
						Срок
					</td>
                <td class="tdClass" width="25%">
                    <SELECT class="select"   onChange=''  style="width:100%;" name="ws"  id="ws" ><OPTION class="option" VALUE="1" >Определенный</OPTION><OPTION class="option" VALUE="2"  selected >Неопределенный</OPTION></SELECT>
                </td>
				
            </tr>
			<tr>
                <td class="tdClass" width=20%>
                    Срок определенный:дата С___
                </td>
                <td>
                    <table border=0 cellpadding=0 cellspacing=0><tr><td valign="middle">
<INPUT class="commonInput" name="sd" id="tsd"  style="width:80px"  value="1992-05-12" ></td><td valign="middle"><input name="sdSelect"  style="width:25px; height:20px" type="button" id="anchorsd"  name="anchorsd"  onclick="try { cal.select(document.form.sd, 'anchorsd','dd-MM-yyyy'); return false;} catch(e){alert(e);}"  value="..." /></td></tr></table>
                </td>
            </tr>
            <!--Nps perenesen na tutor fields -->
            <tr>
                <td class="tdClass" width=20%>
                    Срок определенный:дата по____
                </td>
				<table border=0 cellpadding=0 cellspacing=0><tr><td valign="middle">
				<INPUT class="commonInput" name="sd" id="tsd" style="width:80px" value=""></td><td valign="middle"><input name="sdselected" style="width:25px; height:20px" type="button" id="anchorsd" name="anchorsd" onclick="try {cal.select(document.form.sd,'anchorsd','dd-MM-yyyy'); return false;} catch(e){alert(e);}" value="..." /> </td></tr></table>
				</tr>
				<tr>
			<td class="tdClass" width=20% nowrap>
                 на время замещения временно отсутствующего работника(ФИО)
                </td>
                <td width="80%">
                    <input style="width:100%" class="commonInput" name="ad" value=""
                           maxlength="256"/>
                </td>
				<td class="tdClass" width="25%">
				<SELECT class="select"   onChange=''  style="width:100%;" name="ws"  id="ws" ><OPTION class="option" VALUE="1" >1 год</OPTION><OPTION class="option" VALUE="2"  selected >0,5 год</OPTION></SELECT>
                </td>
            </tr>
			<tr>
			<td class="tdClass" width=20% nowrap>
                 на время замещения временно отсутствующего работника(причина отсутствия)
                </td>
                <td width="80%">
                    <input style="width:100%" class="commonInput" name="ad" value=""
                           maxlength="256"/>
                </td>
            </tr>
			<tr>
                <td class="tdClass" width=20%>
                    Отсутствующего:дата С______
                </td>
                <td>
                    <table border=0 cellpadding=0 cellspacing=0><tr><td valign="middle">
<INPUT class="commonInput" name="sd" id="tsd"  style="width:80px"  value="1992-05-12" ></td><td valign="middle"><input name="sdSelect"  style="width:25px; height:20px" type="button" id="anchorsd"  name="anchorsd"  onclick="try { cal.select(document.form.sd, 'anchorsd','dd-MM-yyyy'); return false;} catch(e){alert(e);}"  value="..." /></td></tr></table>
                </td>
            </tr>
            <!--Nps perenesen na tutor fields -->
            <tr>
                <td class="tdClass" width=20%>  Отсутствующего:дата по_____
                </td>
								                <td>
                    <table border=0 cellpadding=0 cellspacing=0><tr><td valign="middle">
<INPUT class="commonInput" name="" id=""  style="width:80px"  value="1992-05-08" ></td><td valign="middle"><input name="Select"  style="width:25px; height:20px" type="button" id="anchor"  name="anchor"  onclick="try { cal.select(document.form, 'anchor','dd-MM-yyyy'); return false;} catch(e){alert(e);}"  value="..." /></td></tr></table>
                </td>
            </tr>
			 <tr>
			   <td class="tdClass" width="25%">
                    
					Отпуск
                
                    </td>
					<td class="tdClass" width="25%">
					<SELECT class="select"   onChange='null'  style="width:100%;" name="Otpusk"  id="" ><OPTION class="option" VALUE="1" >Декретный</OPTION><OPTION class="option" VALUE="2" >Без содержания</OPTION><OPTION class="option" VALUE="3">Трудовой</OPTION><OPTION class="option" VALUE="4" Selected >Творческий</OPTION></SELECT>
                </td>
			
				            </tr>
			<tr>
                <td class="tdClass" width=20%>
                    Отпуск:дата С______
                
                <td>
                    <table border=0 cellpadding=0 cellspacing=0><tr><td valign="middle">
<INPUT class="commonInput" name="sd" id="tsd"  style="width:80px"  value="1992-05-12" ></td><td valign="middle"><input name="sdSelect"  style="width:25px; height:20px" type="button" id="anchorsd"  name="anchorsd"  onclick="try { cal.select(document.form.sd, 'anchorsd','dd-MM-yyyy'); return false;} catch(e){alert(e);}"  value="..." /></td></tr></table>
                </td>
            </tr>
			<tr>
                <td class="tdClass" width=20%>  Отпуск:дата по_____
                </td>
								                <td>
                    <table border=0 cellpadding=0 cellspacing=0><tr><td valign="middle">
<INPUT class="commonInput" name="" id=""  style="width:80px"  value="1992-06-28" ></td><td valign="middle"><input name="Select"  style="width:25px; height:20px" type="button" id="anchor"  name="anchor"  onclick="try { cal.select(document.form, 'anchor','dd-MM-yyyy'); return false;} catch(e){alert(e);}"  value="..." /></td></tr></table>
                </td>
            </tr>
				  <tr>
                <td class="tdClass" width="25%">
                    Образование
                </td>
                <td class="tdClass" width="25%">
                    <SELECT class="select"   onChange=''  style="width:100%;" name=""  id="" ><OPTION class="option" VALUE="1" >Высшее</OPTION><OPTION class="option" VALUE="2" >Среднее</OPTION><OPTION class="option" VALUE="3"  selected >Среднее-специальное</OPTION></SELECT>
                </td>
				
            </tr>
			<tr>
			<td class="tdClass" width=20% nowrap>
                 ВУЗ
                </td>
                <td width="80%">
                    <input style="width:100%" class="commonInput" name="ad" value="ҚазҰПУ"
                           maxlength="256"/>
                </td>
				</tr>
					<tr>
                <td class="tdClass" width=20% nowrap>
                    Год окончания
                </td>
                <td width="80%">
                    <input style="width:100%" class="commonInput" name="ad" value="2015г."
                           maxlength="256"/>
                </td>
            </tr>
            	<tr>
                <td class="tdClass" width=20% nowrap>
                    Специальност
                </td>
                <td width="80%">
                    <input style="width:100%" class="commonInput" name="ad" value=""
                           maxlength="256"/>
                </td>
            </tr>
				<tr>
                <td class="tdClass" width=20% nowrap>
                    Квалификация
                </td>
                <td width="80%">
                    <input style="width:100%" class="commonInput" name="ad" value=""
                           maxlength="256"/>
                </td>
            </tr>
			 <tr>
                <td class="tdClass" width="25%">
                    Образование(для второе высшего оброзования)
                </td>
                <td class="tdClass" width="25%">
                    <SELECT class="select"   onChange=''  style="width:100%;" name=""  id="" ><OPTION class="option" VALUE="1" >Высшее</OPTION><OPTION class="option" VALUE="2" >Среднее</OPTION><OPTION class="option" VALUE="3"  selected >Среднее-специальное</OPTION></SELECT>
                </td>
				
            </tr>
			<tr>
			<td class="tdClass" width=20% nowrap>
                 ВУЗ
                </td>
                <td width="80%">
                    <input style="width:100%" class="commonInput" name="ad" value="ҚазҰПУ"
                           maxlength="256"/>
                </td>
				</tr>
					<tr>
                <td class="tdClass" width=20% nowrap>
                    Год окончания
                </td>
                <td width="80%">
                    <input style="width:100%" class="commonInput" name="ad" value="2015г."
                           maxlength="256"/>
                </td>
            </tr>
            	<tr>
                <td class="tdClass" width=20% nowrap>
                    Специальност
                </td>
                <td width="80%">
                    <input style="width:100%" class="commonInput" name="ad" value=""
                           maxlength="256"/>
                </td>
            </tr>
				<tr>
                <td class="tdClass" width=20% nowrap>
                    Квалификация
                </td>
                <td width="80%">
                    <input style="width:100%" class="commonInput" name="ad" value=""
                           maxlength="256"/>
                </td>
            </tr>
			 <tr>
                <td class="tdClass" width="25%">
                    Магистратура:образование
                </td>
                <td class="tdClass" width="25%">
                    <SELECT class="select"   onChange=''  style="width:100%;" name=""  id="" ><OPTION class="option" VALUE="1" >Высшее</OPTION><OPTION class="option" VALUE="2" >Среднее</OPTION><OPTION class="option" VALUE="3"  selected >Среднее-специальное</OPTION></SELECT>
                </td>
				
            </tr>
			<tr>
			<td class="tdClass" width=20% nowrap>
                 ВУЗ
                </td>
                <td width="80%">
                    <input style="width:100%" class="commonInput" name="ad" value="ҚазҰПУ"
                           maxlength="256"/>
                </td>
				</tr>
				               <tr>
                <td class="tdClass" width=20%>  Дата окончания________
                </td>
								                <td>
                    <table border=0 cellpadding=0 cellspacing=0><tr><td valign="middle">
<INPUT class="commonInput" name="" id=""  style="width:80px"  value="1992-05-08" ></td><td valign="middle"><input name="Select"  style="width:25px; height:20px" type="button" id="anchor"  name="anchor"  onclick="try { cal.select(document.form, 'anchor','dd-MM-yyyy'); return false;} catch(e){alert(e);}"  value="..." /></td></tr></table>
                </td>
            </tr>
            	<tr>
                <td class="tdClass" width=20% nowrap>
                    Специальност
                </td>
                <td width="80%">
                    <input style="width:100%" class="commonInput" name="ad" value=""
                           maxlength="256"/>
                </td>
            </tr>
				<tr>
                <td class="tdClass" width=20% nowrap>
                    Магистр наук (например:педагогических)
                </td>
                <td width="80%">
                      <td class="tdClass">
        <SELECT class="select"   onChange=''  style="width:100%;" name="sfield"  id="sfield" ><OPTION class="option" VALUE="0" >—</OPTION><OPTION class="option" VALUE="1" >Физико-математические науки</OPTION><OPTION class="option" VALUE="2" >Химические науки</OPTION><OPTION class="option" VALUE="3" >Биологические науки</OPTION><OPTION class="option" VALUE="4" >Технические науки</OPTION><OPTION class="option" VALUE="5" >Сельскохозяйственные науки</OPTION><OPTION class="option" VALUE="6" >Исторические науки</OPTION><OPTION class="option" VALUE="7" >Экономические науки</OPTION><OPTION class="option" VALUE="8" >Философские науки</OPTION><OPTION class="option" VALUE="9" >Филологические науки</OPTION><OPTION class="option" VALUE="10" >Юридические науки</OPTION><OPTION class="option" VALUE="11"  selected >Педагогические науки</OPTION><OPTION class="option" VALUE="12" >Медицинские науки</OPTION><OPTION class="option" VALUE="13" >Фармацевтические науки</OPTION><OPTION class="option" VALUE="14" >Ветеринарные науки</OPTION><OPTION class="option" VALUE="15" >Искусствоведение</OPTION><OPTION class="option" VALUE="16" >Архитектура</OPTION><OPTION class="option" VALUE="17" >Психологические науки</OPTION><OPTION class="option" VALUE="18" >Социологические науки</OPTION><OPTION class="option" VALUE="19" >Политические науки</OPTION><OPTION class="option" VALUE="20" >Культурология</OPTION><OPTION class="option" VALUE="21" >Науки о Земле</OPTION><OPTION class="option" VALUE="22" >Военных наук</OPTION><OPTION class="option" VALUE="23" >Кандидат</OPTION><OPTION class="option" VALUE="24" >Географических наук</OPTION><OPTION class="option" VALUE="25" >Экологии</OPTION><OPTION class="option" VALUE="26" >Естественных наук</OPTION><OPTION class="option" VALUE="27" >Естествознания и биологии</OPTION><OPTION class="option" VALUE="28" >Изобразительное искусство и  черчения</OPTION></SELECT>
    </td>
	</td>
</tr>
<tr>
    <td class="tdClass">
        Отрасль науки
    </td>
    <td class="tdClass">
        <SELECT class="select"   onChange=''  style="width:100%;" name="sfield"  id="sfield" ><OPTION class="option" VALUE="0" >—</OPTION><OPTION class="option" VALUE="1" >Физико-математические науки</OPTION><OPTION class="option" VALUE="2" >Химические науки</OPTION><OPTION class="option" VALUE="3" >Биологические науки</OPTION><OPTION class="option" VALUE="4" >Технические науки</OPTION><OPTION class="option" VALUE="5" >Сельскохозяйственные науки</OPTION><OPTION class="option" VALUE="6" >Исторические науки</OPTION><OPTION class="option" VALUE="7" >Экономические науки</OPTION><OPTION class="option" VALUE="8" >Философские науки</OPTION><OPTION class="option" VALUE="9" >Филологические науки</OPTION><OPTION class="option" VALUE="10" >Юридические науки</OPTION><OPTION class="option" VALUE="11"  selected >Педагогические науки</OPTION><OPTION class="option" VALUE="12" >Медицинские науки</OPTION><OPTION class="option" VALUE="13" >Фармацевтические науки</OPTION><OPTION class="option" VALUE="14" >Ветеринарные науки</OPTION><OPTION class="option" VALUE="15" >Искусствоведение</OPTION><OPTION class="option" VALUE="16" >Архитектура</OPTION><OPTION class="option" VALUE="17" >Психологические науки</OPTION><OPTION class="option" VALUE="18" >Социологические науки</OPTION><OPTION class="option" VALUE="19" >Политические науки</OPTION><OPTION class="option" VALUE="20" >Культурология</OPTION><OPTION class="option" VALUE="21" >Науки о Земле</OPTION><OPTION class="option" VALUE="22" >Военных наук</OPTION><OPTION class="option" VALUE="23" >Кандидат</OPTION><OPTION class="option" VALUE="24" >Географических наук</OPTION><OPTION class="option" VALUE="25" >Экологии</OPTION><OPTION class="option" VALUE="26" >Естественных наук</OPTION><OPTION class="option" VALUE="27" >Естествознания и биологии</OPTION><OPTION class="option" VALUE="28" >Изобразительное искусство и  черчения</OPTION></SELECT>
    </td>
</tr>



<tr>
    <td class="tdClass">
        Ученая степень
    </td>
    <td>
        <SELECT class="select"   onChange=''  style="width:100%;" name="sdg"  id="sdg" ><OPTION class="option" VALUE="1" >—</OPTION><OPTION class="option" VALUE="2"  selected >Кандидат наук</OPTION><OPTION class="option" VALUE="3" >Доктор наук</OPTION><OPTION class="option" VALUE="4" >Магистр</OPTION><OPTION class="option" VALUE="5" >Доктор PhD</OPTION><OPTION class="option" VALUE="6" >Доктор по профилю</OPTION><OPTION class="option" VALUE="7" >Полковник</OPTION><OPTION class="option" VALUE="8" >Подполковник</OPTION><OPTION class="option" VALUE="9" >Подполковник запаса</OPTION><OPTION class="option" VALUE="10" >Полковник в отсавке</OPTION><OPTION class="option" VALUE="11" >Лейтенант</OPTION><OPTION class="option" VALUE="12" >Старший лейтенант</OPTION><OPTION class="option" VALUE="13" >Майор</OPTION><OPTION class="option" VALUE="14" >Полковник запаса</OPTION><OPTION class="option" VALUE="15" >Старшина</OPTION></SELECT>
    </td>
</tr>
<tr>
<td class="tdClass">
Кандидат:каких наук
</td>
<td class="tdClass">
        <SELECT class="select"   onChange=''  style="width:100%;" name="sfield"  id="sfield" ><OPTION class="option" VALUE="0" >—</OPTION><OPTION class="option" VALUE="1" >Физико-математические науки</OPTION><OPTION class="option" VALUE="2" >Химические науки</OPTION><OPTION class="option" VALUE="3" >Биологические науки</OPTION><OPTION class="option" VALUE="4" >Технические науки</OPTION><OPTION class="option" VALUE="5" >Сельскохозяйственные науки</OPTION><OPTION class="option" VALUE="6" >Исторические науки</OPTION><OPTION class="option" VALUE="7" >Экономические науки</OPTION><OPTION class="option" VALUE="8" >Философские науки</OPTION><OPTION class="option" VALUE="9" >Филологические науки</OPTION><OPTION class="option" VALUE="10" >Юридические науки</OPTION><OPTION class="option" VALUE="11"  selected >Педагогические науки</OPTION><OPTION class="option" VALUE="12" >Медицинские науки</OPTION><OPTION class="option" VALUE="13" >Фармацевтические науки</OPTION><OPTION class="option" VALUE="14" >Ветеринарные науки</OPTION><OPTION class="option" VALUE="15" >Искусствоведение</OPTION><OPTION class="option" VALUE="16" >Архитектура</OPTION><OPTION class="option" VALUE="17" >Психологические науки</OPTION><OPTION class="option" VALUE="18" >Социологические науки</OPTION><OPTION class="option" VALUE="19" >Политические науки</OPTION><OPTION class="option" VALUE="20" >Культурология</OPTION><OPTION class="option" VALUE="21" >Науки о Земле</OPTION><OPTION class="option" VALUE="22" >Военных наук</OPTION><OPTION class="option" VALUE="23" >Кандидат</OPTION><OPTION class="option" VALUE="24" >Географических наук</OPTION><OPTION class="option" VALUE="25" >Экологии</OPTION><OPTION class="option" VALUE="26" >Естественных наук</OPTION><OPTION class="option" VALUE="27" >Естествознания и биологии</OPTION><OPTION class="option" VALUE="28" >Изобразительное искусство и  черчения</OPTION></SELECT>
    </td>
	</tr>
<tr>
                <td class="tdClass" width="25%">
                    Дата присвоения
                </td>
                <td class="tdClass" width="25%">
                    <table border=0 cellpadding=0 cellspacing=0><tr><td valign="middle">
<INPUT class="commonInput" name="bd" id="tbd"  style="width:80px"  value="" ></td><td valign="middle"><input name="bdSelect"  style="width:25px; height:20px" type="button" id="anchorbd"  name="anchorbd"  onclick="try { cal.select(document.form.bd, 'anchorbd','dd-MM-yyyy'); return false;} catch(e){alert(e);}"  value="..." /></td></tr></table>
</tr>
<tr>
	<td class="tdClass">
		Специальност
		</td>
		 <td class="tdClass">
		 <input style="width:100%" class="commonInput" name="ad" value="Фмзика 5B-"
                           maxlength="256"/>
                </td>
            </tr>
			<tr>
<td class="tdClass">
Доктор:каких наук
</td>
<td class="tdClass">
        <SELECT class="select"   onChange=''  style="width:100%;" name="sfield"  id="sfield" ><OPTION class="option" VALUE="0" >—</OPTION><OPTION class="option" VALUE="1" >Физико-математические науки</OPTION><OPTION class="option" VALUE="2" >Химические науки</OPTION><OPTION class="option" VALUE="3" >Биологические науки</OPTION><OPTION class="option" VALUE="4" >Технические науки</OPTION><OPTION class="option" VALUE="5" >Сельскохозяйственные науки</OPTION><OPTION class="option" VALUE="6" >Исторические науки</OPTION><OPTION class="option" VALUE="7" >Экономические науки</OPTION><OPTION class="option" VALUE="8" >Философские науки</OPTION><OPTION class="option" VALUE="9" >Филологические науки</OPTION><OPTION class="option" VALUE="10" >Юридические науки</OPTION><OPTION class="option" VALUE="11"  selected >Педагогические науки</OPTION><OPTION class="option" VALUE="12" >Медицинские науки</OPTION><OPTION class="option" VALUE="13" >Фармацевтические науки</OPTION><OPTION class="option" VALUE="14" >Ветеринарные науки</OPTION><OPTION class="option" VALUE="15" >Искусствоведение</OPTION><OPTION class="option" VALUE="16" >Архитектура</OPTION><OPTION class="option" VALUE="17" >Психологические науки</OPTION><OPTION class="option" VALUE="18" >Социологические науки</OPTION><OPTION class="option" VALUE="19" >Политические науки</OPTION><OPTION class="option" VALUE="20" >Культурология</OPTION><OPTION class="option" VALUE="21" >Науки о Земле</OPTION><OPTION class="option" VALUE="22" >Военных наук</OPTION><OPTION class="option" VALUE="23" >Кандидат</OPTION><OPTION class="option" VALUE="24" >Географических наук</OPTION><OPTION class="option" VALUE="25" >Экологии</OPTION><OPTION class="option" VALUE="26" >Естественных наук</OPTION><OPTION class="option" VALUE="27" >Естествознания и биологии</OPTION><OPTION class="option" VALUE="28" >Изобразительное искусство и  черчения</OPTION></SELECT>
    </td>
	</tr>
<tr>
                <td class="tdClass" width="25%">
                    Дата присвоения
                </td>
                <td class="tdClass" width="25%">
                    <table border=0 cellpadding=0 cellspacing=0><tr><td valign="middle">
<INPUT class="commonInput" name="bd" id="tbd"  style="width:80px"  value="" ></td><td valign="middle"><input name="bdSelect"  style="width:25px; height:20px" type="button" id="anchorbd"  name="anchorbd"  onclick="try { cal.select(document.form.bd, 'anchorbd','dd-MM-yyyy'); return false;} catch(e){alert(e);}"  value="..." /></td></tr></table>
</tr>
<tr>
	<td class="tdClass">
		Специальност
		</td>
		 <td class="tdClass">
		 <input style="width:100%" class="commonInput" name="ad" value="Фмзика 5B-"
                           maxlength="256"/>
                </td>
            </tr>
<tr>
<td class="tdClass">
PhD:каких наук
</td>
<td class="tdClass">
        <SELECT class="select"   onChange=''  style="width:100%;" name="sfield"  id="sfield" ><OPTION class="option" VALUE="0" >—</OPTION><OPTION class="option" VALUE="1" >Физико-математические науки</OPTION><OPTION class="option" VALUE="2" >Химические науки</OPTION><OPTION class="option" VALUE="3" >Биологические науки</OPTION><OPTION class="option" VALUE="4" >Технические науки</OPTION><OPTION class="option" VALUE="5" >Сельскохозяйственные науки</OPTION><OPTION class="option" VALUE="6" >Исторические науки</OPTION><OPTION class="option" VALUE="7" >Экономические науки</OPTION><OPTION class="option" VALUE="8" >Философские науки</OPTION><OPTION class="option" VALUE="9" >Филологические науки</OPTION><OPTION class="option" VALUE="10" >Юридические науки</OPTION><OPTION class="option" VALUE="11"  selected >Педагогические науки</OPTION><OPTION class="option" VALUE="12" >Медицинские науки</OPTION><OPTION class="option" VALUE="13" >Фармацевтические науки</OPTION><OPTION class="option" VALUE="14" >Ветеринарные науки</OPTION><OPTION class="option" VALUE="15" >Искусствоведение</OPTION><OPTION class="option" VALUE="16" >Архитектура</OPTION><OPTION class="option" VALUE="17" >Психологические науки</OPTION><OPTION class="option" VALUE="18" >Социологические науки</OPTION><OPTION class="option" VALUE="19" >Политические науки</OPTION><OPTION class="option" VALUE="20" >Культурология</OPTION><OPTION class="option" VALUE="21" >Науки о Земле</OPTION><OPTION class="option" VALUE="22" >Военных наук</OPTION><OPTION class="option" VALUE="23" >Кандидат</OPTION><OPTION class="option" VALUE="24" >Географических наук</OPTION><OPTION class="option" VALUE="25" >Экологии</OPTION><OPTION class="option" VALUE="26" >Естественных наук</OPTION><OPTION class="option" VALUE="27" >Естествознания и биологии</OPTION><OPTION class="option" VALUE="28" >Изобразительное искусство и  черчения</OPTION></SELECT>
    </td>
	</tr>
<tr>
                <td class="tdClass" width="25%">
                    Дата присвоения
                </td>
                <td class="tdClass" width="25%">
                    <table border=0 cellpadding=0 cellspacing=0><tr><td valign="middle">
<INPUT class="commonInput" name="bd" id="tbd"  style="width:80px"  value="" ></td><td valign="middle"><input name="bdSelect"  style="width:25px; height:20px" type="button" id="anchorbd"  name="anchorbd"  onclick="try { cal.select(document.form.bd, 'anchorbd','dd-MM-yyyy'); return false;} catch(e){alert(e);}"  value="..." /></td></tr></table>
</tr>
<tr>
	<td class="tdClass">
		Специальност
		</td>
		 <td class="tdClass">
		 <input style="width:100%" class="commonInput" name="ad" value="Фмзика 5B-"
                           maxlength="256"/>
                </td>
            </tr>			
	<tr>
<td class="tdClass">
Доцент
</td>
<td class="tdClass">
        <SELECT class="select"   onChange=''  style="width:100%;" name="sfield"  id="sfield" ><OPTION class="option" VALUE="1" >ВАК</OPTION><OPTION class="option" VALUE="2" >Университета</OPTION><OPTION class="option" VALUE="3" >Ассоцированный профессор(доцент)</OPTION><OPTION class="option" VALUE="4" Selected>и.о.ассоцированного профессора(доцент)</OPTION></SELECT>
	</td>
	</tr>
<tr>
                <td class="tdClass" width="25%">
                    Дата присвоения
                </td>

                <td class="tdClass" width="25%">
                    <table border=0 cellpadding=0 cellspacing=0><tr><td valign="middle">
<INPUT class="commonInput" name="bd" id="tbd"  style="width:80px"  value="" ></td><td valign="middle"><input name="bdSelect"  style="width:25px; height:20px" type="button" id="anchorbd"  name="anchorbd"  onclick="try { cal.select(document.form.bd, 'anchorbd','dd-MM-yyyy'); return false;} catch(e){alert(e);}"  value="..." /></td></tr></table>
</tr>
	<tr>
<td class="tdClass">
Профессор
</td>
<td class="tdClass">
        <SELECT class="select"   onChange=''  style="width:100%;" name="sfield"  id="sfield" ><OPTION class="option" VALUE="1" >ВАК</OPTION><OPTION class="option" VALUE="2" Selected>Университета</OPTION></SELECT>
	</td>
	</tr>
<tr>
                <td class="tdClass" width="25%">
                    Дата присвоения
                </td>

                <td class="tdClass" width="25%">
                    <table border=0 cellpadding=0 cellspacing=0><tr><td valign="middle">
<INPUT class="commonInput" name="bd" id="tbd"  style="width:80px"  value="" ></td><td valign="middle"><input name="bdSelect"  style="width:25px; height:20px" type="button" id="anchorbd"  name="anchorbd"  onclick="try { cal.select(document.form.bd, 'anchorbd','dd-MM-yyyy'); return false;} catch(e){alert(e);}"  value="..." /></td></tr></table>
</tr>
<tr>	
	<div class='form-group'>
	
			
				<label>Место рождения</label>
				<input type='text' class='form-control' />
			</div></tr>
		</div>
		</div>
	</div> 
@stop