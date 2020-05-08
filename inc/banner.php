<?php 
  $img = '/wp-content/uploads/2020/05/worker_2-1024x614.jpg';
  $header = 'Доставка в любую точку России всего за 70&nbsp;руб!';
  $text = 'Трек для отслеживания приходит в SMS в течении 2-х рабочих дней.';
  $buttons = array(
    "Условия доставки" => "delivery",
    "Отследить заказ" => "chto-s-zakazom",
  );
?>

<div class="action-banner" style="background-image: url(<?php echo $img; ?>);">
	<div class="ab-wrapper">
		<div class="ab-header"><?php echo $header; ?></div>
		<div class="ab-text"><?php echo $text; ?></div>
		<div class="ab-buttons">
			<?php foreach ($buttons as $name => $link): ?>
				<button onclick="document.location='<?php echo $link; ?>'"><?php echo $name; ?></button>
			<?php endforeach; ?>
		</div>
	</div>
</div>