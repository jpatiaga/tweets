function hideTweet(id) {
	if (!confirm("Are you sure you want to hide this tweet?")) {
		return false;
	}
	$("#tweet_" + id).hide('slow');
	$.get("/index.php/post/hide?id=" + id);
}