<script type="text/javascript">
{literal}
function ViewList() {
	document.location=$('#ListPage').val();
}

function ViewAdd() {
	document.location=$('#AddPage').val();
}
{/literal}
</script>

<h2>Sound Manager</h2>

{if $Message!= ""}
<p class="success_message">
{if     $Message == "CREATE_FOLDER" } Successfully created a new sound folder.
{elseif $Message == "MODIFY_FOLDER" } Successfully modified the sound folder. 
{elseif $Message == "DELETE_FOLDER" } Successfully deleted sound folder.
{/if}
</p>
{/if}

<p>
	Create and manage the recordings for your IVRs and call queues.
</p>

<table class="fullwidth">
	<tr>
		<td>
			<small>View:</small>
			<select id="ListPage" onchange="ViewList()">
				<option selected="selected" value="SoundEntries_List.php?PK_SoundFolder=">All Sounds</option>
				<option selected="selected" value="SoundFolders_List.php">All Folders</option>
				<option value="SoundLanguages_List.php">All Languages</option>
				<optgroup label="Folder Listings">
				{foreach from=$SoundFolders item=Folder}
					<option value="SoundEntries_List.php?PK_SoundFolder={$Folder.PK_SoundFolder}">{$Folder.Name} Folder ({$Folder.Quantity} files)</option>
				{/foreach}
				</optgroup>
			</select>
		</td>
		<td style="text-align: right;">
			<small>Add New:</small>
			<select id="AddPage">
				<option value="SoundFiles_Modify.php">Sound</option>
				<option selected="selected" value="SoundFolders_Modify.php">Folder</option>
				<option value="SoundLanguages_Modify.php">Language</option>
			</select>
			<button onclick="ViewAdd()">Go</button>
		</td>
	</tr>
</table>

<br />
<hr style="background-color: #ccc"/>
<br />

<table class="listing fullwidth">
	<caption>Sound Folders ( {$Start+1} to {$End} ) of {$Total}</caption>
	<tr>
		<th>
			<a href="?Sort=Name">Folder Name</a>
			{if $Sort == "Name"}
				<img src="../static/images/{$Order}.gif" alt="{$Order}" />
			{/if}
		</th>
		<th>
			<a href="?Sort=Quantity">Folder Quantity</a>
			{if $Sort == "Quantity"}
				<img src="../static/images/{$Order}.gif" alt="{$Order}" />
			{/if}
		</th>
		<th>
			<a href="?Sort=Type">Folder Type</a>
			{if $Sort == "Type"}
				<img src="../static/images/{$Order}.gif" alt="{$Order}" />
			{/if}
		</th>
		<th style="width: 130px"></th>
	</tr>
	
	{foreach from=$Folders item=Folder}
	<tr class="{if $Hilight == $Folder._PK_}hilight{/if} {cycle values="odd,even"}">
		<td>{$Folder.Name}</td>
		<td>{$Folder.Quantity}</td>
		<td>{$Folder.Type}</td>
		<td>
			{if $Folder.Type == 'User'}
			<form method="get" action="SoundFolders_Modify.php" style="display: inline;">
				<input type="hidden" name="PK_SoundFolder" value="{$Folder._PK_}" />
				<button type="submit" name="submit" value="modify">Modify</button>
			</form>
			
			<form method="get" action="SoundFolders_Delete.php" style="display: inline;">
				<input type="hidden" name="PK_SoundFolder" value="{$Folder._PK_}" />
				<button type="submit" name="submit" value="delete" class="important">Delete</button>
			</form>
			{else}
				<button class="disabled">Modify</button>
				<button class="important_disabled">Delete</button>
			{/if}
		</td>
	</tr>
	{/foreach}
</table>

<p style="text-align: right">
{if $Start > 0} 
	<a class="prev" href="?Start={$Start-$PageSize}">Previous</a>
{/if}
{if $End < $Total}
<a class="next" href="?Start={$Start+$PageSize}">Next</a>
{/if}
</p>
