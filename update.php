<?php
require_once('auth.php');

if(!file_exists("../../".$ne_config_info['website_conf_filename']) || !file_exists("../../".$ne_config_info['variables_conf_filename'])){
	header('Location: website_editor.php');
}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Live Update - <?php echo($ne_config_info['app_titleplain']); ?></title>

<?php echo NavTools::includeHtml("default"); ?>

<script type="text/javascript">
	var sv = "";
	var tv = "";
	var goback = false;

	function check() {
		$("#imgLoading").show();
		$.post("app/live_update.php", {
			"oper": "check_update"
		}, function(rdata) {
			var jdata = JSON.parse(rdata);
			$("#imgLoading").hide();
			if(jdata.error != "") {
				$("#errorMsg").html(jdata.error);
			} else {
				$("#cur_ver").html(jdata.current_version);
				if(jdata.has_stable_update == false) {
					$("#stb_ver").html(jdata.stable_version);
                                        $("#stb_changelog").html(jdata.stable_chlog);
					sv = jdata.stable_version;
					$("#btnDoUpdate").removeAttr("disabled");
					goback = true;
				} else {
					$("#stb_ver").html("<span style='color:red;'>"
                                            + jdata.stable_version
                                            + "</span>");
                                        $("#stb_changelog").html(jdata.stable_chlog);
					sv = jdata.stable_version;
					$("#btnDoUpdate").removeAttr("disabled");
				}

				if(jdata.has_test_update == true) {
					$("#tst_ver").html(jdata.test_version);
                                        $("#tst_changelog").html(jdata.test_chlog);
					tv = jdata.test_version;
					$("#btnDoTestUpdate").removeAttr("disabled");
				}
			}
		});
	}

	$(document).ready(function() {
		$("#btnDoUpdate").click(function() {
			if(goback) {
				if(!confirm("Sind Sie sicher, dass Sie auf eine alte stabile Version wechseln wollen?")) {
					return;
				}
			}
			$("#imgLoading").show();
			$("#btnDoUpdate").attr("disabled", "true");
			$.post("app/live_update.php", {
				"oper": "do_update",
				"uv": sv
			}, function(rdata) {
				$("#imgLoading").hide();
				alert(rdata);
				location.reload();
			});
		});

		$("#btnDoTestUpdate").click(function() {
			if(!confirm("Sind Sie sicher, dass Sie auf eine Testversion wechseln wollen?")) {
				return;
			}
			$("#imgLoading").show();
			$("#btnDoTestUpdate").attr("disabled", "true");
			$.post("app/live_update.php", {
				"oper": "do_update",
				"uv": tv
			}, function(rdata) {
				$("#imgLoading").hide();
				alert(rdata);
				location.reload();
			});
		});

		$("#btnCheckUpdate").click(function() {
			check();
		});
	});


</script>

</head>

<body id="bd_Update" onload="check();">
	<?php require('common_nav_menu.php'); ?>

	<div class="update container page">

        <div class="page-header">
            <h2 class="page-header">Update <small>Aktualisieren Sie hier den NavEditor</small></h2>
            <div class="pull-right">
				<input class="btn btn-primary btn-light" type="button" id="btnUpdate" name="btnUpdate" value="Speichern" />
	        </div>
        </div>


		<div id="updateInfo">

			<!--<div class="row">
				<div class="update-cat col-md-2">
					<span>Aktuell verwendete Version:</span>
				</div>
				<div class="col-md-1">
					<span id="cur_ver"><?php //echo($ne_config_info['version']); ?></span>
				</div>
			</div>

			<div class="row">
				<div class="update-cat col-md-2">
					<span>Letzte offizielle Testversion:</span>
				</div>
				<div class="col-md-1">
					<span id="tst_ver"></span>
				</div>
				<div class="col-md-6">
					<b>Letzte Änderungen:</b> <pre id="tst_changelog" class="changelog"></pre>
				</div>
				<div class="col-md-2">
					<input type="button" id="btnDoTestUpdate" class="btn btn-warning btn-light" value="Diese Testversion installieren" disabled="true" />
				</div>
			</div>

			<div class="row">
				<div class="update-cat col-md-2">
					<span>Stabile Version:</span>
				</div>
				<div class="col-md-1">
					<span id="stb_ver"></span>
				</div>
				<div class="col-md-6">
					<b>Letzte Änderungen:</b><pre id="stb_changelog" class="changelog"></pre>
				</div>
				<div class="col-md-2">
					<input type="button" id="btnDoUpdate" class="btn btn-success btn-light" value="Diese Version installieren" disabled="true" />
				</div>
			</div>

			<hr />
			<h3>Alternativer Layout-Vorschlag (funktioniert aber noch nicht)</h3>-->

			<div class="panel panel-info">
				<div class="panel-heading"><h3 class="panel-title">Aktuell verwendete Version: <span id="cur_ver"><?php echo($ne_config_info['version']); ?></span></h3></div>
				<div class="panel-body">
					<input class="btn btn-primary btn-light" type="button" id="btnCheckUpdate" name="btnCheckUpdate" value="Informationen aktualisieren" />
				</div>
			</div>

			<div class="panel panel-success">
				<div class="panel-heading">
					<h3 class="panel-title">Aktuelle stabile Version: <span id="stb_ver"></span></h3>
				</div>
				<div class="panel-body">
					<p><b>Letzte Änderungen:</b></p>
					<div class="col-md-8">
						<pre id="stb_changelog" class="changelog"></pre>
					</div>
					<div class="col-md-4">
						<input type="button" id="btnDoUpdate" class="btn btn-success btn-light" value="Installieren" disabled="true" />
					</div>
				</div>
			</div>

			<div class="panel panel-warning">
				<div class="panel-heading">
					<h3 class="panel-title">Letzte offizielle Testversion: <span id="tst_ver"></span></h3>
				</div>
				<div class="panel-body">
					<p><b>Letzte Änderungen:</b></p>
					<div class="col-md-8">
						<pre id="tst_changelog" class="changelog"></pre>
					</div>
					<div class="col-md-4">
						<input type="button" id="btnDoTestUpdate" class="btn btn-warning btn-light" value="Testversion installieren" disabled="true" />
					</div>
				</div>
			</div>
		</div>

		<img webdi="imgLoading" src="ajax-loader.gif" border="0" width="16" height="16" style="display:none;" />
		<div id="errorMsg" style="color:red;"></div>
	</div>

	<?php require('common_footer.php'); ?>
</body>

</html>
