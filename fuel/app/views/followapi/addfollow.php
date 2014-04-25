<?php echo Asset::css('bootstrap.css'); ?>
<form method="post" action="/twitter/public/followapi/addfollowpost">
<table>
	<tr>
    	<td>ID user:</td>
        <td><input type="text" id="iduser" name="iduser" value="<?php if(!empty($iduser)) echo $iduser; ?>" /></td>
    </tr>
    <tr>
    	<td>Followed ID user:</td>
        <td><input type="text" id="followed_iduser" name="followed_iduser"  value="<?php if(!empty($followed_iduser)) echo $followed_iduser; ?>" /></td>
    </tr>
    <tr>
    	<td colspan="2"><input type="submit" id="submit" name="submit" value="Sign up" /></td>
    </tr>
</table>
</form>

<table class="result_table">
	<tr>
		<td>Status</td>
		<td><input type="text" id="status" name="status" value"<?php if(!empty($status)) echo $status; ?>" /></td>
	</tr>
	<tr>
		<td>Message</td>
		<td><input type="text" id="message" name="message" value"<?php if(!empty($message)) echo $message; ?>" /> </td>
	</tr>
</table>
