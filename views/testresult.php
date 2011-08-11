<?php if(isset($error_msg)): ?>
<div class="error_wrap">
 <div class="icon error">
    <div class="symbol">!</div>
</div>
<span><?php echo $error_msg; ?></span>
</div>
<?php exit; ?>
<?php endif; ?>

<?php if(isset($stats['time']) && isset($stats['total'])): ?>
<div id="suite_total">
    Ran <?php echo $stats['total'] ?> tests in <?php echo round($stats['time'],2) ?> seconds
    <div id="suite_count">
        <?php echo "Passed: {$stats['passed']}&nbsp;&nbsp;Failed: {$stats['failed']}&nbsp;&nbsp;Skipped: {$stats['skipped']}"; ?>
    </div>
</div>
<?php endif; ?>

<?php foreach($runner->getResults() as $key => $suite): ?>
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
        <?php foreach($suite['results'] as $result): ?>
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
            <td class="empty">&nbsp;</td>
            <td colspan="3" class="message">
                <pre><?php echo $result->compactTrace(); ?></pre>
            </td>
        </tr>
        <tr><td class="empty" colspan="4">&nbsp;</td></tr>
        <?php endif; ?>
        <?php endforeach; ?>
    </tbody>
    <tfoot></tfoot>
</table>
<?php endif; ?>
<?php endforeach; ?>