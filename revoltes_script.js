function validation_heure(nom_zone_heure) {
var ok=true;
var dat= document.getElementById(nom_zone_heure);
      
reg = new RegExp("^(([0-1]?[0-9])|([2][0-3])):([0-5]?[0-9])(:([0-5]?[0-9]))?$");
    
if(dat.value.match(reg))
	{//alert("l'heure est bonne");
	ok=true;}
else
	{alert("l'heure est invalide"); 
	dat.value="hh:mm";ok=false;}
return ok;
}

function show_calendar(str_target, str_datetime) {
   var arr_months = ["Janvier", "FÃ©vrier", "Mars", "Avril", "Mai", "Juin", "Juillet", "AoÃ»t", "Septembre", "Octobre", "Novembre", "DÃ©cembre"];
   var week_days = ["Di", "Lu", "Ma", "Me", "Je", "Ve", "Sa"];
   var n_weekstart = 1; // day week starts from (normally 0 or 1)
 
// Si la date est invalide : Initialisation avec la date du jour
var test_date = /^(\d+)\/(\d+)\/(\d+)$/;
if (!test_date.exec(str_datetime)) { 	var d = new Date();
											var str_datetime = d.getDate()+ "/" +(d.getMonth() + 1) + "/"+  d.getFullYear();
										 }
 
 
   var dt_datetime = (str_datetime == null || str_datetime =="" ?  new Date() : str2dt(str_datetime));
 
/* JOUR MOIS ET ANNEE PRESENTE*/
   var this_Day = new Date().getDate()
   var this_Month = new Date().getMonth()
	var this_Year = new Date().getFullYear()
 
/* MOIS PRECEDENT */
   var dt_prev_month = new Date(dt_datetime);
   dt_prev_month.setMonth(dt_datetime.getMonth()-1);
   var dt_next_month = new Date(dt_datetime);
   dt_next_month.setMonth(dt_datetime.getMonth()+1);
 
/* ANNEE PRECEDENTE */   
   var dt_prev_year = new Date(dt_datetime);
   dt_prev_year.setFullYear(dt_datetime.getFullYear()-1);
   var dt_next_year = new Date(dt_datetime);
   dt_next_year.setFullYear(dt_datetime.getFullYear()+1);
 
/* PREMIER JOUR DU MOIS */
   var dt_firstday = new Date(dt_datetime);
   dt_firstday.setDate(1);
   dt_firstday.setDate(1-(7+dt_firstday.getDay()-n_weekstart)%7);
 
/* DERNIER JOUR DU MOIS    */
   var dt_lastday = new Date(dt_next_month);
   dt_lastday.setDate(0);
 
/* TITRE DU CALENDRIER */
   //var title_calendar=(str_target=='dd')?'DATE D\'ARRIVEE':'DATE DE DEPART';
   var title_calendar='DATE DE TRAJET';
 
   // html generation (feel free to tune it for your particular application)
   // ENTETE CALENDRIER
   var StringYear=dt_datetime.getFullYear().toString().substring(dt_datetime.getFullYear().toString().length-2,dt_datetime.getFullYear().toString().length)
   var str_buffer = new String (
      "<table class=\"clsOTable\" cellspacing=\"0\" border=\"0\" width=\"100%\">\n"+
      "<tr>\n<td bgcolor=\"#4682B4\">"+
      "<table cellspacing=\"1\" cellpadding=\"3\" border=\"0\" width=\"100%\">\n"+
 
   //ENTETE TABLEAU
   "<tr>\n   <td bgcolor=\"blue\" style='text-align:center;color:white;font-family:verdana;font-weight:bold;font-size:11px;' colspan=\"6\">"+title_calendar+"<\/td>\n"+
   "<td bgcolor=\"blue\" style=\"text-align:center;color:navy;font-family:verdana;font-weight:bold;font-size:10px;background-color:silver;border:outset 2px white;cursor:pointer;\" onclick=\"document.getElementById('DivCalendar').style.display='none';\">X<\/td>\n "+
   "<\/tr>\n"+
 
 
 
// Ligne AnnÃ©e prÃ©cÃ©dente / suivante
      "<tr>\n   <td bgcolor=\"blue\"><a href=\"javascript:show_calendar('"+str_target+"', '"+ 
      dt2dtstr(dt_prev_year)+"');\" title=\"année précédente\">"+
      //"<img src=\"Images/prev.ico\" width=\"16\" height=\"16\" border=\"0\""+
      //" alt=\"AnnÃ©e prÃ©cÃ©dente\"></a></td>\n"+
	  " <font color=\"white\" face=\"tahoma, verdana\" size=\"2\">< </font></a></td>\n"+
      "   <td align=\"center\" bgcolor=\"blue\" colspan=\"5\">"+
      "<font color=\"white\" face=\"tahoma, verdana\" size=\"2\">"
      +" 20"+StringYear+"</font></td>\n"+
      "   <td bgcolor=\"blue\" align=\"right\"><a href=\"javascript:show_calendar('"
      +str_target+"', '"+dt2dtstr(dt_next_year)+"');\" title=\"année suivante\">"+
      //"<img src=\"Images/next.ico\" width=\"16\" height=\"16\" border=\"0\" alt=\"AnnÃ©e suivante\"></a></td>\n</tr>\n"+
	  " <font color=\"white\" face=\"tahoma, verdana\" size=\"2\"> > </font> </a></td>\n</tr>\n"+
 
// Ligne Mois prÃ©cÃ©dent / suivant
      "<tr>\n   <td bgcolor=\"#4682B4\"><a href=\"javascript:show_calendar('"+
      str_target+"', '"+ dt2dtstr(dt_prev_month)+"'); \">"+
      //"<img src=\"Images/prev.ico\" width=\"16\" height=\"16\" border=\"0\" alt=\"MP\" title=\""+dt_prev_month+"\"></a></td>\n"+
	  "<font color=\"white\" face=\"tahoma, verdana\" size=\"2\"><< </font></a></a></td>\n"+
      "   <td align=\"center\" bgcolor=\"#4682B4\" colspan=\"5\">"+
      "<font color=\"white\" face=\"tahoma, verdana\" size=\"2\">"
      +arr_months[dt_datetime.getMonth()]+" 20"+StringYear+"</font></td>\n"+
      "   <td bgcolor=\"#4682B4\" align=\"right\"><a href=\"javascript:show_calendar('"
      +str_target+"', '"+dt2dtstr(dt_next_month)+"');  \">"+
      //"<img src=\"Images/next.ico\" width=\"16\" height=\"16\" border=\"0\" alt=\"MS\"  title=\""+dt_next_month+"\" ></a></td>\n</tr>\n"
	  "<font color=\"white\" face=\"tahoma, verdana\" size=\"2\"> >> </font></a></td>\n</tr>\n"
 
   ); //end newstring
 
   var dt_current_day = new Date(dt_firstday);
 
   // print weekdays titles
   str_buffer += "<tr>\n";
   for (var n=0; n<7; n++)
      str_buffer += "   <td bgcolor=\"#87CEFA\">"+
      "<font color=\"white\" face=\"tahoma, verdana\" size=\"2\">"+
      week_days[(n_weekstart+n)%7]+"</font></td>\n";
 
 
   // print calendar table
   str_buffer += "</tr>\n";
   while (dt_current_day.getMonth() == dt_datetime.getMonth() ||
      dt_current_day.getMonth() == dt_firstday.getMonth()) {
 
      // print row header
      str_buffer += "<tr>\n";
      for (var n_current_wday=0; n_current_wday<7; n_current_wday++) {
            if (dt_current_day.getDate() == this_Day &&  dt_current_day.getMonth() == this_Month && dt_current_day.getFullYear()==this_Year ){
 
              // case aujourd'hui
               str_buffer += "   <td bgcolor=\"#FFB6C1\" align=\"right\">";
			   }
 
            else {
            		if (dt_current_day.getDay() == 0 || dt_current_day.getDay() == 6){
	               	// case de weekend 
   		            	str_buffer += "   <td bgcolor=\"#DBEAF5\" align=\"right\">";
   		            	}
		          else{
       	        	// jour ouvres du mois
          		     	str_buffer += "   <td bgcolor=\"white\" align=\"right\">";
						}
				  }
 
 
 str_buffer += "<a style=\"cursor:pointer;\" onclick=\"document.getElementById('"+str_target+"').value='"+dt2dtstr(dt_current_day)+"'; document.getElementById('DivCalendar').style.display='none'; \">"+
               "<font color=\"black\" face=\"tahoma, verdana\" size=\"2\">";

               str_buffer += dt_current_day.getDate()+"</font></a></td>\n"; 
 
 
            dt_current_day.setDate(dt_current_day.getDate()+1);
      }
 
 
      // print row footer
      str_buffer += "</tr>\n";
   }
 
   // print calendar footer
   str_buffer +=  "</tr>\n</td>\n</table>\n" ;
 
 
 
   fenCalendrier=document.getElementById('DivCalendar');
   fenCalendrier.innerHTML=str_buffer;
   fenCalendrier.style.top=document.getElementById(str_target).offsetTop+"px";
   fenCalendrier.style.left=Number(document.getElementById(str_target).offsetLeft)+Number(document.getElementById(str_target).offsetWidth)+200+"px";
   fenCalendrier.style.display="block"

}


// datetime parsing and formatting routines. modify them if you wish other datetime format
function str2dt (str_datetime) {
   var re_date = /^(\d+)\/(\d+)\/(\d+)$/;
   if (!re_date.exec(str_datetime))
      return alert("Invalid Datetime format: "+ str_datetime);
   return (new Date (RegExp.$3, RegExp.$2-1, RegExp.$1, RegExp.$4, RegExp.$5, RegExp.$6));
}
 
 
 
/***********************************
* Formatage de date pour affichage *
***********************************/
 
function dt2dtstr (dt_datetime) {
   var FormatedDate=""
 
   FormatedDate+=(dt_datetime.getDate().toString().length==1)?"0"+dt_datetime.getDate().toString():dt_datetime.getDate().toString();
   FormatedDate+="/";
   FormatedDate+=((dt_datetime.getMonth()+1).toString().length==1)?"0"+(dt_datetime.getMonth()+1).toString():(dt_datetime.getMonth()+1).toString();
   FormatedDate+="/";
   FormatedDate+="20"+dt_datetime.getFullYear().toString().substring(dt_datetime.getFullYear().toString().length-2,dt_datetime.getFullYear()).toString()
   return FormatedDate;
}
 
function dt2tmstr (dt_datetime) {
   return (new String (dt_datetime.getHours()+":"+dt_datetime.getMinutes()+":"+dt_datetime.getSeconds()));
}
 
 
 
/*************************
* comparaison des dates  *
*************************/
function comparedate(){
 
var maintenant=new Date()
maintenant=dt2dtstr(maintenant).split('\/').reverse().join('')
 
 
 
if(document.getElementById('da').value.length<1){
                                             alert("Entrez une date d\'arrivee");
                                             return false;
                                             }
 
var date_arrivee=document.getElementById('da').value.split('\/').reverse().join('');
if(date_arrivee<maintenant){
                             alert('vous ne pourrez pas arriver avant aujourd\'hui');
                             return false;
                             }
 
if(document.getElementById('da').value.length<1){
                                                 alert("Entrez une date de départ");
                                                 return false;
                                                 }
 
var date_depart=document.getElementById('dd').value.split('\/').reverse().join('');
if(date_arrivee>date_depart){
                             alert("Vous devez arriver avant de repartir!");
                             return false;
                             }
 
 
else {     
      return true;}
 
}


/*******************************************************************
              ajax XMLHttpRequest
******************************************************************/
function getXhr(){
	var xhr = null; 
	if(window.XMLHttpRequest) // Firefox et autres
	xhr = new XMLHttpRequest(); 
	else if(window.ActiveXObject){ // Internet Explorer 
	try {
		xhr = new ActiveXObject("Msxml2.XMLHTTP");
	} catch (e) {
		xhr = new ActiveXObject("Microsoft.XMLHTTP");
	            }
		}
	else { // XMLHttpRequest non supporté par le navigateur 
	   alert("Votre navigateur ne supporte pas les objets XMLHTTPRequest..."); 
	   xhr = false; 
	} 
        return xhr;
	}
 
	/**
	* Méthode qui sera appelée sur le click du bouton
	*/
function go(){
	var xhr = getXhr();
// Ici on va voir comment faire du post
	xhr.open("POST","ajaxhoraire.php",true); // true => travail asynchrone
	
// ne pas oublier ça pour le post
	xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
				
// ne pas oublier de poster les arguments
// ici, l'id de la gare
	gare = document.getElementById('gare_arrivee');
	id_gare = gare.options[gare.selectedIndex].value;
	//date = document.getElementById('date').value;
	
	param="stop_id="+id_gare; //+"&date="+date;
				
// envoi des paramètres
	xhr.send(param);

// On définit ce qu'on va faire quand on aura la réponse
	xhr.onreadystatechange = function(){
// On ne fait quelque chose que si on a tout reçu et que le serveur est ok
		if(xhr.readyState == 4 && xhr.status == 200){
			leselect = xhr.responseText;
// On se sert de innerHTML pour rajouter les options a la liste
			document.getElementById('h_arr_theo').innerHTML = leselect;
			}
		}
}

function calcule_heures() {

selectplagehoraire = document.getElementById('plage_horaire');
//alert(selectplagehoraire);
selectedplage = selectplagehoraire.selectedIndex;
//alert(selectedplage);
codeplage=selectplagehoraire.options[selectedplage].value;
//alert('plage horaire :'+codeplage);

heuredepart=document.getElementById('h_debut');
heurearr=document.getElementById('h_fin');


switch (codeplage) {
	case '00':
		{
		heuredepart.value = '00:00:00';
		heurearr.value='05:00:00';
		break;
		}
	case '05':
		{
		heuredepart.value = '05:01:00';
		heurearr.value='06:00:00';
		break;
		}
	case '06':
		{
		heuredepart.value = '06:01:00';
		heurearr.value='07:00:00';
		break;
		}
	case '07':
		{
		heuredepart.value = '07:01:00';
		heurearr.value='08:00:00';
		break;
		}
	case '08':
		{
		heuredepart.value = '08:01:00';
		heurearr.value='09:00:00';
		break;
		}
	case '09':
		{
		heuredepart.value = '09:01:00';
		heurearr.value='10:00:00';
		break;
		}
	case '10':
		{
		heuredepart.value = '10:01:00';
		heurearr.value='11:00:00';
		break;
		}
	case '11':
		{
		heuredepart.value = '11:01:00';
		heurearr.value='12:00:00';
		break;
		}
	case '12':
		{
		heuredepart.value = '12:01:00';
		heurearr.value='13:00:00';
		break;
		}
	case '13':
		{
		heuredepart.value = '13:01:00';
		heurearr.value='14:00:00';
		break;
		}
	case '14':
		{
		heuredepart.value = '14:01:00';
		heurearr.value='15:00:00';
		break;
		}
	case '15':
		{
		heuredepart.value = '15:01:00';
		heurearr.value='16:00:00';
		break;
		}
	case '16':
		{
		heuredepart.value = '16:01:00';
		heurearr.value='17:00:00';
		break;
		}
	case '17':
		{
		heuredepart.value = '17:01:00';
		heurearr.value='18:00:00';
		break;
		}
	case '18':
		{
		heuredepart.value = '18:01:00';
		heurearr.value='19:00:00';
		break;
		}
	case '19':
		{
		heuredepart.value = '19:01:00';
		heurearr.value='20:00:00';
		break;
		}
	case '20':
		{
		heuredepart.value = '20:01:00';
		heurearr.value='21:00:00';
		break;
		}
	case '21':
		{
		heuredepart.value = '21:01:00';
		heurearr.value='23:59:00';
		break;
		}
	}
	//alert('heure dep/arr :'+heuredepart.value+'/'+heurearr.value);
	return true;
}
