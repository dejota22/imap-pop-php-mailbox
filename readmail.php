<?



/**
 * @see https://github.com/dejota22/php-imap
 * @author Dyemerson almeida https://www.linkedin.com/pub/dyemerson-almeida/38/795/653
 */

$mailboxes = array(

	array(
		'label' 	=> '',//just a label for header of html layout
		'mailbox' 	=> '',//example pop : mail.bol.com.br:110/pop , imap : {imap.gmail.com:993/imap/ssl}
		'username' 	=> '', // mail box user 
		'password' 	=> '' ,//mail box password
	)
);





function decode_imap_text($str){
    $result = '';
    $decode_header = imap_mime_header_decode($str);
    foreach ($decode_header AS $obj) {
        $result .= htmlspecialchars(rtrim($obj->text, "\t"));
	}
    return $result;
}
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<link href="css/demo.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div id="wrapper">
<div id="main">


	
	<div id="mailboxes">
	<? if (!count($mailboxes)) { ?>
		<p>Please configure at least one IMAP mailbox.</p>
	<? } else { 

		foreach ($mailboxes as $current_mailbox) {
			?>
			<div class="mailbox">
			<h2><?=$current_mailbox['label']?></h2>
			<?
			if (!$current_mailbox['enable']) {
			?>
				<p>This mailbox is disabled.</p>
			<?
			} else {
				
				
				$stream = imap_open($current_mailbox['mailbox'], $current_mailbox['username'], $current_mailbox['password']);


				
				if (!$stream) { 
				?>
					<p>Could not connect to: <?=$current_mailbox['label']?>. Error: <?=imap_last_error()?></p>
				<?
				} else {
					
					$emails = imap_search($stream, 'SUBJECT " this a subject " SINCE '. date('d-M-Y',strtotime("-1 day")));
					
					if (!count($emails)){
					?>
						<p>No e-mails found.</p>
					<?
					} else {

						 rsort($emails);
						
						foreach($emails as $email_id){
						
							$overview = imap_fetch_overview($stream,$email_id,0);
							$body = trim(substr(imap_body($stream, $email_id), 0, 100));	

							?>
							<div class="email_item clearfix <?=$overview[0]->seen?'read':'unread'?>"> <? // add a different class for seperating read and unread e-mails ?>
								<span class="subject" title="<?=decode_imap_text($overview[0]->subject)?>"><?=decode_imap_text($overview[0]->subject)?></span>
								<span class="from" title="<?=decode_imap_text($overview[0]->from)?>"><?=decode_imap_text($overview[0]->from)?></span>
								<span class="date"><?=$overview[0]->date?></span>
								<span class="body"><?=$body?></span>
							</div>
							<?
						} 
					} 
					imap_close($stream); 
				}
				
			} 
			?>
			</div>
			<?
		}
	} ?>
	</div>
	
</div>


</div>
</body>
</html>
