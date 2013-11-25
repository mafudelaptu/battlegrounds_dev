<!-- name, sirname, country, age, level of education, his background in -->
<!-- esports (did he work in any orga), was he involved in any projects in -->
<!-- real life, what is he good at? how can he contribute to the organization -->
<div class="page-header">
<h1>Jobs @ N-GAGE.TV</h1>
</div>
{if $status}
	<div class="alert alert-error">
		{$status}
	</div>
{/if}
<form class="form-horizontal" method="post" action="jobs.php" id="jobsForm">
	<div class="control-group">
		<label class="control-label" for="inputName">Full Name</label>
		<div class="controls">
			<input type="text" id="inputName" placeholder="Name and Surname" name="inputName">
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="inputEmail">Email</label>
		<div class="controls">
			<input type="text" id="inputEmail" placeholder="Your Email-adress" name="inputEmail">
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="inputCountry">Country</label>
		<div class="controls">
			<input type="text" id="inputCountry" placeholder="Your Country" name="inputCountry">
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="inputAge">Age</label>
		<div class="controls">
			<input type="text" id="inputAge" placeholder="Your Age"name="inputAge">
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="inputLvlEdu">Level of education</label>
		<div class="controls">
			<input type="text" id="inputLvlEdu" placeholder="Level of education" name="inputLvlEdu">
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="inputBGEsports">Background in eSports</label>
		<div class="controls">
			<textarea rows="5" cols="120" placeholder="Your background in eSports (worked in any organisation etc...)" id="inputBGEsports" name="inputBGEsports"></textarea>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="inputProjects">involved in any projects before?</label>
		<div class="controls">
			<input type="text" id="inputProjects" placeholder="involved in any projects before? If yes, which one?" name="inputProjects">
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="inputGoodAt">What are u good at?</label>
		<div class="controls">
			<input type="text" id="inputGoodAt" placeholder="What are u good at? Any special skills?" name="inputGoodAt">
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="inputContribute">How can you contribute to N-GAGE.TV?</label>
		<div class="controls">
			<textarea placeholder="How can he contribute to N-GAGE.TV?" id="inputContribute" name="inputContribute"></textarea>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="inputOptional">Anything you would like to add?</label>
		<div class="controls">
			<textarea placeholder="optional" id="inputOptional" name="inputOptional"></textarea>
		</div>
	</div>
	<div class="control-group">
		<div class="controls">
			<button type="submit" class="btn" name="submit" value="submit">Send</button>
		</div>
	</div>
</form>
