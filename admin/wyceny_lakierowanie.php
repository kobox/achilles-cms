<?
include('wyceny_header.php');

if($_POST[typ_kopia]){
	copy_in_table("lakierowanie",$_GET[typ],$_POST[typ_kopia],$_POST[id_copy]);
}

if($_POST[zapisz]){
	$_POST[cena]=str_replace(",",".",$_POST[cena]);
	if(!$_POST[lakierowanie_typ])$bug[lakierowanie_typ]="<br/><span class='label label-important'>Brak typu lakierowanie.</span>";
	if(!$_POST[lakierowanie_typ_en])$_POST[lakierowanie_typ_en]=$_POST[lakierowanie_typ];
	if(!$_POST[szt_od] && $_POST[szt_od]!=0)$bug[szt_od]="<br/><span class='label label-important'>Brak liczyb sztuk od.</span>";
	if(!$_POST[cena] && $_POST[cena]!=0 && !$_POST[cena_szt])$bug[cena]="<br/><span class='label label-important'>Brak ceny.</span>";
	
	if(!$_POST[szt_do] && $_POST[szt_od] && !$_POST[bez_granicy])$_POST[szt_do]=$_POST[szt_od];
	if($_POST[bez_granicy] == 1)$_POST[szt_do]=0;

	if(!is_numeric($_POST[cena]) || !is_numeric($_POST[szt_od]) || !is_numeric($_POST[szt_do])){
		$bug[numer]="<br/><span class='label label-important'>Zakres sztuk i cena muszą być liczbami.</span>";
	}
	
	if($bug){
		if($_POST[zapisz]=="add"){
			$_GET[add]=1;
			$alert="Formularz dodawania lakierowania zawiera błędy";
		}
		if($_POST[zapisz]=="edit"){
			$_GET[edit_id]=$_POST[edit_id];
			$alert="Formularz edycji lakierowania zawiera błędy";
		}
	}else{
		if($_POST[zapisz]=="add"){
			$sql="INSERT INTO lakierowanie ";
			$sql.="(lakierowanie_typ,lakierowanie_typ_en,szt_od,szt_do,cena,cena_szt,cena_typ,waluta,typ) VALUES ";
			$sql.="('$_POST[lakierowanie_typ]','$_POST[lakierowanie_typ_en]','$_POST[szt_od]','$_POST[szt_do]','$_POST[cena]','$_POST[cena_szt]','$_POST[cena_typ]','$_POST[waluta]','$_GET[typ]')";
			if(!mysql_query($sql)){
				$alert="Dodanie lakierowania nie powiodło się <br/>".mysql_error();
			}else{
				$alert_ok="Lakierowanie zostało dodany do bazy.";
			}
		}
		if($_POST[zapisz]=="edit"){
			$sql="UPDATE lakierowanie SET ";
			$sql.="lakierowanie_typ='$_POST[lakierowanie_typ]',lakierowanie_typ_en='$_POST[lakierowanie_typ_en]',szt_od='$_POST[szt_od]',szt_do='$_POST[szt_do]',cena='$_POST[cena]',cena_szt='$_POST[cena_szt]',cena_typ='$_POST[cena_typ]',waluta='$_POST[waluta]'";
			$sql.=" WHERE id=$_POST[edit_id]";
			if(!mysql_query($sql)){
				$alert="Edycja lakierowania nie powiodła się <br/>".mysql_error()."<br/>".$sql;
			}else{
				$alert_ok="Lakierowanie zaktualizowany.";
			}
		}
	}
}

if($_GET[del_id]){
	$sql="UPDATE lakierowanie SET del='1' WHERE id=$_GET[del_id]";
	if(!mysql_query($sql)){
		$alert="Usunięcie lakierowania nie powiodło się <br/>".mysql_error();
	}else{
		$alert_ok="Lakierowanie zostało usunięte z bazy.";
	}
}
?>
<?include('wyceny_menu.php')?>
        </div><!--/span-->
        <div class="span10">
		<?if($alert){?>
			<div class="alert alert-error">
				<button type="button" class="close" data-dismiss="alert">×</button>
				<strong>Wystapił błąd!</strong>&nbsp;<?=$alert?>
			</div>
		<?}?>
		<?if($alert_ok){?>
			<div class="alert alert-success">
				<button type="button" class="close" data-dismiss="alert">×</button>
				<strong>Operacja powiodła się!</strong>&nbsp;<?=$alert_ok?>
			</div>
		<?}?>
		<div class="row-fluid">
            <div class="span12">
					<div class="row-fluid">
						<div class="span12">
						<form name="wycena">
						<input type="hidden" name="site" value="<?=$_GET[site]?>">
							<fieldset>
							<div class="row-fluid">
								<div class="span12">
									<legend>Lakierowanie&nbsp;&nbsp;&nbsp;
									<?select_typ();?>
									<a class="btn btn-mini btn-primary" role="button" href="?site=<?=$_GET[site]?>&typ=<?=$_GET[typ]?>&add=1">Dodaj lakierowanie</a>
									</legend>
								</div>
							</div>
							</fieldset>
						</form>						
						</div>
						<div class="row-fluid">
						<div class="span12">
							<fieldset>
							<div class="row-fluid">
								<div class="span12">
									<?
										if($_GET[edit_id] || $_GET[add]){									
											$lists=array("lakierowanie_typ"=>"");
											foreach($lists as $key => $val){
												$sql="SELECT DISTINCT $key as dana FROM lakierowanie WHERE del='0'";
												$res=mysql_query($sql);									
												while($row=mysql_fetch_array($res)){
													if($lists[$key])$lists[$key].=",";
													$lists[$key].="&quot;".$row[dana]."&quot;";
												}
											}
										}
									?>
									<form name="edycja" method="POST" action="?site=<?=$_GET[site]?>&typ=<?=$_GET[typ]?>">
										<table class="table table-striped table-hover">
										<thead align="center">
											<th width="5"><input type="checkbox" id="id_copy_all" name="id_copy_all" value="all" ></th>
											<th>Rodzaj lakierowania</th>
											<th>Rodzaj lakierowania EN</th>
											<th>Liczba sztuk</th>
											<th>Cena całość</th>
											<th>Cena szt.</th>
											<th></th>
											<th>Edycja</th>
										</thead>
										<?
										if($_GET[add]){?>
											<tr>
											<td></td>
											<td>
												<input name="lakierowanie_typ" value="<?=$_POST[lakierowanie_typ]?>" data-source="[<?=$lists[lakierowanie_typ]?>]" type="text" data-provide="typeahead" class="input-medium" autocomplete="off">
												<?echo $bug[lakierowanie_typ];?>
											</td>
											<td>
												<input name="lakierowanie_typ_en" value="<?=$_POST[lakierowanie_typ]?>" data-source="[<?=$lists[lakierowanie_typ_en]?>]" type="text" data-provide="typeahead" class="input-medium" autocomplete="off">
												<?echo $bug[lakierowanie_typ_en];?>
											</td>
											<td>
												od <input name="szt_od" value="<?=$_POST[szt_od]?>" data-source="[<?=$lists[szt_od]?>]" type="text" data-provide="typeahead" class="input-mini" autocomplete="off">&nbsp;do
												<input name="szt_do" value="<?=$_POST[szt_do]?>" data-source="[<?=$lists[szt_do]?>]" type="text" data-provide="typeahead" class="input-mini" autocomplete="off">
												<br/><input type="checkbox" name="bez_granicy" value="1"> bez granicy
												<?echo $bug[szt_od];?>
											</td>
											<td>
												<input name="cena" value="<?=$_POST[cena]?>" data-source="[<?=$lists[cena]?>]" type="text" data-provide="typeahead" class="input-mini" autocomplete="off">
												<?echo $bug[cena];?>
											</td>
											<td>
												<input name="cena_szt" value="<?=$_POST[cena_szt]?>" data-source="[<?=$lists[cena_szt]?>]" type="text" data-provide="typeahead" class="input-mini" autocomplete="off">
												<select name="cena_typ" class="span8">
													<option value="nadwyżka" <?echo ($_POST[cena_typ]=="nadwyżka"?"selected":"")?>>nadwyżka
													<option value="całość" <?echo ($_POST[cena_typ]=="całość"?"selected":"")?>>całość
												</select>
												<?echo $bug[cena_szt];?>
											</td>
											<td>
												<select name="waluta" class="span9">
													<option value="pln" <?echo ($_POST[waluta]==pln?"selected":"")?>>pln
													<option value="eur" <?echo ($_POST[waluta]==eur?"selected":"")?>>eur
													<option value="usd" <?echo ($_POST[waluta]==usd?"selected":"")?>>usd
												</select>												
											</td>
											<td>
												<input type="hidden" name="zapisz" value="add">
												<button class="btn btn-mini btn-danger" type="button" onClick="document.forms['edycja'].submit()">Zapisz</button>
												<a href="?site=<?=$_GET[site]?>&typ=<?=$_GET[typ]?>"><i class="icon-remove"></i>anuluj</a>
											</td>
											</tr>
										<?}
										
										$sql="SELECT * FROM lakierowanie WHERE typ='$_GET[typ]' AND del='0' ORDER BY lakierowanie_typ,szt_od";
										$res=mysql_query($sql);
										while($dane=mysql_fetch_array($res)){
											if($_GET[edit_id]==$dane[id]){
												$_POST=$dane;
											?>
												<tr>
												<td></td>
												<td>
													<input name="lakierowanie_typ" value="<?=$_POST[lakierowanie_typ]?>" data-source="[<?=$lists[lakierowanie_typ]?>]" type="text" data-provide="typeahead" class="input-medium" autocomplete="off">
													<?echo $bug[lakierowanie_typ];?>
												</td>
												<td>
													<input name="lakierowanie_typ_en" value="<?=$_POST[lakierowanie_typ_en]?>" data-source="[<?=$lists[lakierowanie_typ]?>]" type="text" data-provide="typeahead" class="input-medium" autocomplete="off">
													<?echo $bug[lakierowanie_typ_en];?>
												</td>
												<td>
													od <input name="szt_od" value="<?=$_POST[szt_od]?>" data-source="[<?=$lists[szt_od]?>]" type="text" data-provide="typeahead" class="input-mini" autocomplete="off">&nbsp;do
													<input name="szt_do" value="<?=$_POST[szt_do]?>" data-source="[<?=$lists[szt_do]?>]" type="text" data-provide="typeahead" class="input-mini" autocomplete="off">
													<br/>
													<input type="checkbox" name="bez_granicy" value="1"
													<?if($_POST[szt_od] && $_POST[szt_do]==0)echo "checked";?>
													> bez granicy													
													<?echo $bug[szt_od];?>
												</td>
												<td>
													<input name="cena" value="<?=$_POST[cena]?>" data-source="[<?=$lists[cena]?>]" type="text" data-provide="typeahead" class="input-mini" autocomplete="off">
													<?echo $bug[cena];?>
												</td>
												<td>
													<input name="cena_szt" value="<?=$_POST[cena_szt]?>" data-source="[<?=$lists[cena_szt]?>]" type="text" data-provide="typeahead" class="input-mini" autocomplete="off">
													<select name="cena_typ" class="span8">
														<option value="nadwyżka" <?echo ($_POST[cena_typ]=="nadwyżka"?"selected":"")?>>nadwyżka
														<option value="całość" <?echo ($_POST[cena_typ]=="całość"?"selected":"")?>>całość
													</select>
													<?echo $bug[cena_szt];?>
												</td>
												<td>
													<select name="waluta" class="span9">
														<option value="pln" <?echo ($_POST[waluta]==pln?"selected":"")?>>pln
														<option value="eur" <?echo ($_POST[waluta]==eur?"selected":"")?>>eur
														<option value="usd" <?echo ($_POST[waluta]==usd?"selected":"")?>>usd
													</select>												
												</td>
												<td>
													<input type="hidden" name="zapisz" value="edit">
													<input type="hidden" name="edit_id" value="<?=$_GET[edit_id]?>">
													<button class="btn btn-mini btn-danger" type="button" onClick="document.forms['edycja'].submit()">Zapisz</button>
													<a href="?site=<?=$_GET[site]?>&typ=<?=$_GET[typ]?>"><i class="icon-remove"></i>anuluj</a>
												</td>
												</tr>
											<?
											}else{
												echo "<tr>";
												echo "<td><input type='checkbox' name='id_copy[]' value='".$dane[id]."'></td>";
												echo "<td>";
												echo $dane[lakierowanie_typ];
												echo "</td><td>";
												echo $dane[lakierowanie_typ_en];
												echo "</td><td>";
												if($dane[szt_od] && $dane[szt_do]){
													echo "od ".$dane[szt_od]." do ".$dane[szt_do];
												}elseif($dane[szt_od] == 0 && $dane[szt_do]){
													echo "do ".$dane[szt_do];
												}elseif($dane[szt_od] && $dane[szt_do]==0){
													echo "od ".$dane[szt_od];
												}
												echo "</td>";
												echo "<td>";if($dane[cena]>0){echo $dane[cena]." ".$dane[waluta];}else{echo "-";}echo "</td>";
												echo "<td>";if($dane[cena_szt]>0){echo $dane[cena_szt]." ".$dane[waluta]." ".$dane[cena_typ];}else{echo "-";} echo "</td>";
												echo "<td></td>";
												echo "<td><a href='?site=".$_GET[site]."&edit_id=".$dane[id]."#f".$dane[id]."'><i class='icon-pencil'></i>edytuj</a> ";
												echo "<a href='?site=".$_GET[site]."&del_id=".$dane[id]."' onClick=\"if(confirm('Chcesz usunąć lakierowanie ?')){return true;}else{return false;}\"><i class='icon-trash'></i>usun</a></td>";
												echo "</tr>";
											}
										}
										?>
										<tr><td></td><td colspan="7">Kopiuj do&nbsp;
										<?select_typ("typ_kopia",$_GET[typ],0,1);?>
										<button class="btn btn-mini btn-success" type="button" onClick="document.forms['edycja'].submit()">Kopiuj</button>
										</td>
										</tr>
										</table>
									</form>
								</div>
							</div>
							</fieldset>
						</div>
					</div>
			</div>
			</div><!--/span-->
        </div><!--/span-->
      </div><!--/row-->
	</div><!--/.fluid-container-->
<script>
$('#id_copy_all').click(
    function()
    {
        $("input[type='checkbox']").attr('checked', $('#id_copy_all').is(':checked'));    
    }
)
</script>	
<?include('wyceny_footer.php');?>