

        case "edit_export":
        case "edit_export_ifthere":			                                 
            new jsWindow(url,"edit_module",-1,-1,970,760,true,true,true,true);
	break;
            
		case "help_export":            
              var fo=false;
             for(var k=jsWindow_count-1;k>-1;k--){			                                     
              eval("if(jsWindow"+k+"Object.ref=='edit_module'){ fo=true;wind=jsWindow"+k+"Object.wind}");	                          
              if(fo) break;
		     }
		     wind.focus();            
          <?php if($online_help):?>                          
              if(arguments[1]) url="/webEdition/getHelp.php?hid="+arguments[1];
              else url="/webEdition/getHelp.php";
              new jsWindow(url,"help",-1,-1,800,600,true,false,true,true);                        
            <?php else:?>            
              url="/webEdition/noAvailable.php";
              new jsWindow(url,"help_no_available",-1,-1,380,140,true,false,true);                        
            <?php endif?>
            	break;                       
        case "new_export":			                                             
        case "new_export_group":			                                             
        case "save_export":			                                             
        case "delete_export":
        case "exit_export":			                                                 
        case "start_export":
             var fo=false; 
             for(var k=jsWindow_count-1;k>-1;k--){			                                     
              eval("if(jsWindow"+k+"Object.ref=='edit_module'){ jsWindow"+k+"Object.wind.content.we_cmd('"+arguments[0]+"');fo=true;wind=jsWindow"+k+"Object.wind}");	                          
              if(fo) break;
		     }
		     wind.focus();            
            break;                    
        case "unlock":
			we_repl(self.load,url,arguments[0]);
		    break;
        
           
           
            
