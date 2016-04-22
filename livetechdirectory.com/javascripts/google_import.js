google.load('search', '1');

function gup_num()
{
  name_num = name_num.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
  var regexS = "[\\?&]"+name_num+"=([^&#]*)";
  var regex = new RegExp( regexS );
  var results = regex.exec( window.location.href );
  if( results == null )
    return "";
  else
    return results[1];
}

function gup_what()
{
  name_what = name_what.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
  var regexS = "[\\?&]"+name_what+"=([^&#]*)";
  var regex = new RegExp( regexS );
  var results = regex.exec( window.location.href );
  if( results == null )
    return "";
  else
    return results[1];
}

function gup_lr()
{
  name_lr = name_lr.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
  var regexS = "[\\?&]"+name_lr+"=([^&#]*)";
  var regex = new RegExp( regexS );
  var results = regex.exec( window.location.href );
  if( results == null )
    return "";
  else
    return "'"+results[1]+"'";
}
function ajaxSearch()
{
var xmlHttp;
try
  {
  // Firefox, Opera 8.0+, Safari
  xmlHttp=new XMLHttpRequest();
  }
catch (e)
  {
  // Internet Explorer
  try
    {
    xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
    }
  catch (e)
    {
    try
      {
      xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
      }
    catch (e)
      {
      alert("Your browser does not support AJAX!");
      return false;
      }
    }
  }
  xmlHttp.onreadystatechange=function()
    {
    if(xmlHttp.readyState==4)
      {
    	//RESPONSE TEXT HERE
    	
        //document.myForm.time.value=xmlHttp.responseText;
//        document.getElementById("time").innerHTML=xmlHttp.responseText;
    	
    	var sURL = unescape(window.location.href);
    	window.location.href = sURL;


      }
    }
  
  	var params = "import="+encodeURIComponent(print_res);
  	xmlHttp.open("POST","../spider_list.php",true);
 	xmlHttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
 	xmlHttp.setRequestHeader("Content-Length", params.length);

 	xmlHttp.send(params);
 	
}

var print_res = "";
var name_num = "num";
var name_what = "as_q";
var name_lr = "lr";

function searchG() { 
	
		// Create a search control 
		var searchControl = new google.search.SearchControl(); 
		searchControl.setResultSetSize(google.search.Search.LARGE_RESULTSET);
		
		// Add web searchers 
		var ws = new google.search.WebSearch()
		
		//set language description
		ws.setRestriction(google.search.Search.RESTRICT_EXTENDED_ARGS, {lr:gup_lr()});
		searchControl.addSearcher(ws); 
		
		searchControl.draw(null); 

	  	var page = 0;
		var aux = 0;
		
		var wanted_results = gup_num();
		var what = gup_what();
		
		
		var result = new Array();
		
		var titles = "";
		var urls = "";
		var contents = "";
		ws.setSearchCompleteCallback(null, 
			function() {
				if (ws.cursor !== undefined) {
					var wanted_pages = Math.ceil(wanted_results / 8);
					var no_pages = ws.cursor.pages.length;
	
					if (wanted_pages<no_pages) {
						no_pages = wanted_pages;
					}
					
					// loop through results
					for (i = 0; i < ws.results.length; i++) {
						aux = 8*page + i + 1;
						
						if (aux <= wanted_results) {
							print_res = print_res + ws.results[i].titleNoFormatting+"||"+ws.results[i].url+"||"+ws.results[i].content+"[end_line]";
						}	
					}
	
					if (page < no_pages){
						page++;
						ws.gotoPage(page);
					} else {
						//this is where i've got all the results
//						alert(print_res);
						ajaxSearch();
						ws.gotoPage(13);
					}
				} else {
					document.getElementById('noreserror').style.display = 'block';
				}
			}
		);

		// execute the search 
		searchControl.execute(decodeURIComponent(what)); 
		
   } 

google.setOnLoadCallback(searchG); 


