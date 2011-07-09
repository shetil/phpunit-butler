<?php foreach($suiteResults as $key => $suite): ?>
<?php if($key): ?>
<table class="results">
    <thead>	
	<tr>
        <th class="test"><?php echo $key; ?></th>
		<th class="time">Time</th>
		<th class="memory">Memory</th>
		<th class="message">Message</th>
	</tr>
	</thead>
    <tbody> 
        <?php foreach($suite->getResults() as $result): ?>
        <?php $trace = $result->status == Butler\TestResult::ERROR && $result->compactTrace(); ?>
        <tr class="<?php echo $result->statusName(); ?> <?php echo $trace ? 'trace' : ''; ?>">
            <td class="test" title="<?php echo $result->statusName();  ?>">
            <div class="icon <?php echo $result->statusName();  ?>">
                <div class="symbol"><?php echo $result->statusSymbol(); ?></div>
            </div>    
            <span><?php echo $result->name; ?></span>
            </td>
            <td class="time"><?php echo $result->formatTime(); ?></td>
            <td class="memory"><?php echo $result->formatMemory(); ?></td>
            <td class="message"><?php echo $result->formatMessage(); ?></td>
        </tr>
        <?php if($trace): ?>
        <tr class="error trace-output">
            <td colspan="4" class="message">
                <pre><?php echo $result->compactTrace(); ?></pre>
            </td>
        </tr>
        <tr><td class="empty" colspan="4">&nbsp;</td></tr>
        <?php endif; ?>
        <?php endforeach; ?>
        <tr>
            <td class="test">Code Coverage</td>
            <td class="message" colspan="3">&nbsp;</td>
        </tr>
    </tbody>
    <tfoot></tfoot>
</table>
<?php endif; ?>
<?php endforeach; ?>