<script type="text/javascript" language="JavaScript1.2" src="/ci4/menu/jsdomenu.js"></script>
<script type="text/javascript" language="JavaScript1.2" src="/ci4/menu/jsdomenubar.js"></script>
<script type="text/javascript" language="JavaScript1.2">
function createjsDOMenu()
{
<?PHP
    $query = $this->db->query("UPDATE intermenu SET target='_self' WHERE target IS NULL ");
    $query = $this->db->query("SELECT MID(modulo,1,1) ind ,modulo, CONCAT( UPPER(MID(titulo,1,1)),LOWER(MID(titulo,2,100))) titulo, ejecutar FROM intermenu WHERE modulo > 9 AND target!='NO' ORDER BY modulo ");
    if ($query->num_rows() > 0) {
	$i = 0;
	foreach ($query->result() as $row) {
	    if ( $i <> $row->ind ) {
		$i = $row->ind;
		if ( $i > 1 ) { echo "}\n"; };
		echo "absoluteMenu$i = new jsDOMenu(160, \"absolute\");\n";
		echo "with (absoluteMenu$i) { \n";
            };
	    echo "   addMenuItem(new menuItem(\"".$row->titulo."\", \"\", \"".$row->ejecutar."\"));\n";
	};
    };

    echo "}\n";
    echo "absoluteMenuBar = new jsDOMenuBar(\"static\", \"staticMenuBar\");";
    echo "with (absoluteMenuBar) {";

    $query = $this->db->query("SELECT modulo, CONCAT( UPPER(MID(titulo,1,1)), LOWER(MID(titulo,2,100))) tit1 FROM intermenu WHERE modulo < 10 ORDER BY modulo");
    if ($query->num_rows() > 0) {
	foreach ($query->result() as $row) {
	    echo "   addMenuBarItem(new menuBarItem(\"".$row->tit1."\", absoluteMenu".$row->modulo.", \"item".$row->modulo."\"));\n" ;
	    echo "   absoluteMenuBar.items.item".$row->modulo.".showIcon(\"icon1\", \"icon2\", \"icon3\");\n";
	};
	    
    };
?>
    }
}
</script>
