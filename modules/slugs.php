<?php
/**
 * Slug Optimizer Module
 * 
 * @version 1.0
 * @since 0.9
 */

if (class_exists('SU_Module')) {

class SU_Slugs extends SU_Module {
	
	function get_menu_title() { return __('Slug Optimizer', 'seo-ultimate'); }
	
	function admin_page_contents() {
		$this->admin_form_start();
		$this->textarea('words_to_remove', __('Words to Remove', 'seo-ultimate'), 20);
		$this->admin_form_end();
	}
	
	function init() {
		
		//Only sanitize if a permalink is being requested via AJAX
		if ($_POST['action'] == 'sample-permalink')
			//The filter priority is very important to ensure our function runs before WordPress's sanitize_title_with_dashes() function
			add_filter('sanitize_title', array($this, 'remove_words_from_title'), 9);
	}
	
	function remove_words_from_title($title) {
		
		if (strcmp($title, $_POST['new_title']) == 0) {
			//An empty slug was given, so the post title is being used as the default! Call to action!
			
			//Remove the stopwords from the slug
			$newtitle = implode("-", array_diff(explode(" ", $title), explode("\n", $this->get_setting('words_to_remove'))));
			
			//Make sure we haven't removed too much!
			if (!empty($newtitle)) return $newtitle;
		}
		
		return $title;
	}
	
	function admin_dropdowns() {
		return array(
			  'overview' => __('Overview', 'seo-ultimate')
			, 'faq' => __('FAQ', 'seo-ultimate')
		);
	}
	
	function admin_dropdown_overview() {
		return __("
<ul>
	<li><p><strong>What it does:</strong> Slug Optimizer removes common words from the portion of a post&#8217;s or Page&#8217;s URL that is based on its title. (This portion is also known as the &#8220;slug.&#8221;)</p></li>
	<li><p><strong>Why it helps:</strong> Slug Optimizer increases keyword potency because there are fewer words in your URLs competing for relevance.</p></li>
	<li><p><strong>How to use it:</strong> Slug Optimizer goes to work when you&#8217;re editing a post or Page, with no action required on your part.
		If needed, you can use the textbox below to customize which words are removed.</p></li>
</ul>
", 'seo-ultimate');
	}
	
	function admin_dropdown_faq() {
		return __("
<h6>What's a slug?</h6>
<p>The slug of a post or page is the portion of its URL that is based on its title.</p>
<p>When you edit a post or Page in WordPress, the slug is the yellow-highlighted portion of the Permalink beneath the Title textbox.</p>

<h6>Does the Slug Optimizer change my existing URLs?</h6>
<p>No. Slug Optimizer will not relocate your content by changing existing URLs. Slug Optimizer only takes effect on new posts and pages.</p>

<h6>How do I see Slug Optimizer in action?</h6>
<ol>
	<li>Create a new post/Page in WordPress.</li>
	<li>Type in a title containing some common words.</li>
	<li>Click outside the Title box. WordPress will insert a URL labeled &#8220;Permalink&#8221; below the Title textbox. The Slug Optimizer will have removed the common words from the URL.</li>
</ol>

<h6>Why didn't the Slug Optimizer remove common words from my slug?</h6>
<p>It's possible that every word in your post title is in the list of words to remove. In this case, Slug Optimizer doesn't remove the words, because if it did, you'd end up with a blank slug.</p>

<h6>What if I want to include a common word in my slug?</h6>
<p>When editing the post or page in question, just click the Edit button next to the permalink and change the slug as desired.</p>

<h6>How do I revert back to the optimized slug after making changes?</h6>
<p>When editing the post or page in question, just click the Edit button next to the permalink; a Save button will appear in its place. Next erase the contents of the textbox, and then click the aforementioned Save button.</p>
", 'seo-ultimate');
	}
	
	function get_default_settings() {
		
		//Special thanks to the "SEO Slugs" plugin for the stopwords array.
		//http://wordpress.org/extend/plugins/seo-slugs/
		$defaults = array ("a", "able", "about", "above", "abroad", "according", "accordingly", "across", "actually", "adj", "after", "afterwards", "again", "against", "ago", "ahead", "ain't", "all", "allow", "allows", "almost", "alone", "along", "alongside", "already", "also", "although", "always", "am", "amid", "amidst", "among", "amongst", "an", "and", "another", "any", "anybody", "anyhow", "anyone", "anything", "anyway", "anyways", "anywhere", "apart", "appear", "appreciate", "appropriate", "are", "aren't", "around", "as", "a's", "aside", "ask", "asking", "associated", "at", "available", "away", "awfully", "b", "back", "backward", "backwards", "be", "became", "because", "become", "becomes", "becoming", "been", "before", "beforehand", "begin", "behind", "being", "believe", "below", "beside", "besides", "best", "better", "between", "beyond", "both", "brief", "but", "by", "c", "came", "can", "cannot", "cant", "can't", "caption", "cause", "causes", "certain", "certainly", "changes", "clearly", "c'mon", "co", "co.", "com", "come", "comes", "concerning", "consequently", "consider", "considering", "contain", "containing", "contains", "corresponding", "could", "couldn't", "course", "c's", "currently", "d", "dare", "daren't", "definitely", "described", "despite", "did", "didn't", "different", "directly", "do", "does", "doesn't", "doing", "done", "don't", "down", "downwards", "during", "e", "each", "edu", "eg", "eight", "eighty", "either", "else", "elsewhere", "end", "ending", "enough", "entirely", "especially", "et", "etc", "even", "ever", "evermore", "every", "everybody", "everyone", "everything", "everywhere", "ex", "exactly", "example", "except", "f", "fairly", "far", "farther", "few", "fewer", "fifth", "first", "five", "followed", "following", "follows", "for", "forever", "former", "formerly", "forth", "forward", "found", "four", "from", "further", "furthermore", "g", "get", "gets", "getting", "given", "gives", "go", "goes", "going", "gone", "got", "gotten", "greetings", "h", "had", "hadn't", "half", "happens", "hardly", "has", "hasn't", "have", "haven't", "having", "he", "he'd", "he'll", "hello", "help", "hence", "her", "here", "hereafter", "hereby", "herein", "here's", "hereupon", "hers", "herself", "he's", "hi", "him", "himself", "his", "hither", "hopefully", "how", "howbeit", "however", "hundred", "i", "i'd", "ie", "if", "ignored", "i'll", "i'm", "immediate", "in", "inasmuch", "inc", "inc.", "indeed", "indicate", "indicated", "indicates", "inner", "inside", "insofar", "instead", "into", "inward", "is", "isn't", "it", "it'd", "it'll", "its", "it's", "itself", "i've", "j", "just", "k", "keep", "keeps", "kept", "know", "known", "knows", "l", "last", "lately", "later", "latter", "latterly", "least", "less", "lest", "let", "let's", "like", "liked", "likely", "likewise", "little", "look", "looking", "looks", "low", "lower", "ltd", "m", "made", "mainly", "make", "makes", "many", "may", "maybe", "mayn't", "me", "mean", "meantime", "meanwhile", "merely", "might", "mightn't", "mine", "minus", "miss", "more", "moreover", "most", "mostly", "mr", "mrs", "much", "must", "mustn't", "my", "myself", "n", "name", "namely", "nd", "near", "nearly", "necessary", "need", "needn't", "needs", "neither", "never", "neverf", "neverless", "nevertheless", "new", "next", "nine", "ninety", "no", "nobody", "non", "none", "nonetheless", "noone", "no-one", "nor", "normally", "not", "nothing", "notwithstanding", "novel", "now", "nowhere", "o", "obviously", "of", "off", "often", "oh", "ok", "okay", "old", "on", "once", "one", "ones", "one's", "only", "onto", "opposite", "or", "other", "others", "otherwise", "ought", "oughtn't", "our", "ours", "ourselves", "out", "outside", "over", "overall", "own", "p", "particular", "particularly", "past", "per", "perhaps", "placed", "please", "plus", "possible", "presumably", "probably", "provided", "provides", "q", "que", "quite", "qv", "r", "rather", "rd", "re", "really", "reasonably", "recent", "recently", "regarding", "regardless", "regards", "relatively", "respectively", "right", "round", "s", "said", "same", "saw", "say", "saying", "says", "second", "secondly", "see", "seeing", "seem", "seemed", "seeming", "seems", "seen", "self", "selves", "sensible", "sent", "serious", "seriously", "seven", "several", "shall", "shan't", "she", "she'd", "she'll", "she's", "should", "shouldn't", "since", "six", "so", "some", "somebody", "someday", "somehow", "someone", "something", "sometime", "sometimes", "somewhat", "somewhere", "soon", "sorry", "specified", "specify", "specifying", "still", "sub", "such", "sup", "sure", "t", "take", "taken", "taking", "tell", "tends", "th", "than", "thank", "thanks", "thanx", "that", "that'll", "thats", "that's", "that've", "the", "their", "theirs", "them", "themselves", "then", "thence", "there", "thereafter", "thereby", "there'd", "therefore", "therein", "there'll", "there're", "theres", "there's", "thereupon", "there've", "these", "they", "they'd", "they'll", "they're", "they've", "thing", "things", "think", "third", "thirty", "this", "thorough", "thoroughly", "those", "though", "three", "through", "throughout", "thru", "thus", "till", "to", "together", "too", "took", "toward", "towards", "tried", "tries", "truly", "try", "trying", "t's", "twice", "two", "u", "un", "under", "underneath", "undoing", "unfortunately", "unless", "unlike", "unlikely", "until", "unto", "up", "upon", "upwards", "us", "use", "used", "useful", "uses", "using", "usually", "v", "value", "various", "versus", "very", "via", "viz", "vs", "w", "want", "wants", "was", "wasn't", "way", "we", "we'd", "welcome", "well", "we'll", "went", "were", "we're", "weren't", "we've", "what", "whatever", "what'll", "what's", "what've", "when", "whence", "whenever", "where", "whereafter", "whereas", "whereby", "wherein", "where's", "whereupon", "wherever", "whether", "which", "whichever", "while", "whilst", "whither", "who", "who'd", "whoever", "whole", "who'll", "whom", "whomever", "who's", "whose", "why", "will", "willing", "wish", "with", "within", "without", "wonder", "won't", "would", "wouldn't", "x", "y", "yes", "yet", "you", "you'd", "you'll", "your", "you're", "yours", "yourself", "yourselves", "you've", "z", "zero");
	
		return array(
			  'words_to_remove' => implode("\n", $defaults)
		);
	}
}

}
?>