<?php 

class 					Trader {

	private 			$actualValue;
	private 			$mobileAverage;

	private 			$totalDay;
	private 			$totalMoney;

	private 			$nbActions = 0;
	private 			$money;

	private 			$isUnderLow = false;

	private 			$isHigherHigh = false;

	private 			$history = array();

	public function		__construct() {

	}

	public function 	setTotalDay($totalDay) {
		$this->totalDay = $totalDay;
	}

	public function 	getTotalDay() {
		return ($this->totalDay);
	}

	public function 	getTotalMoney() {
		return ($this->totalMoney);
	}

	public function 	setTotalMoney($totalMoney) {
		$this->totalMoney = $totalMoney;
		$this->money = $totalMoney;
	}

	public function 	calculPriceBuy($nbActions) {
		$price = $nbActions * $this->actualValue;
		$tax = ceil($price * 0.0015);
		return ($price + $tax);
	}

	public function 	calculPriceSell($nbActions) {
		$price = $nbActions * $this->actualValue;
		$tax = ceil($price * 0.0015);
		return ($tax);
	}

	public function 	canBuy($nbActions) {
		if ($this->calculPriceBuy($nbActions) <= $this->money) {
			return (true);
		} else {
			return (false);
		}
	}

	public function 	qtyMaxBuy() {
		$i = 0;
		while ($this->canBuy($i))
			$i++;
		return ($i - 1);
	}

	public function 	canSell($nbActions) {
		if ($this->nbActions >= $nbActions && $this->calculPriceSell($nbActions) <= $this->money) {
			return (true);
		} else {
			return (false);
		}
	}

	public function 	buy($nbActions) {
		echo "buy " . $nbActions . "\n";
		$this->money -= $this->calculPriceBuy($nbActions);
		$this->nbActions += $nbActions;
	}

	public function 	sell($nbActions) {
		echo "sell " . $nbActions . "\n";
		$this->nbActions -= $nbActions;
		$this->money -= $this->calculPriceSell($nbActions);
		$this->money += $this->actualValue * $nbActions;
	}

	public function 	highMobileAverage() {
		$tmp = array();
		$i = count($this->history) - 1;
		$j = 0;
		while ($i >= 0 && $j < 5) {
			$tmp[] = max($this->history[$i]);
			$i--;
			$j++;
		}
		$avg = array_sum($tmp) / count($tmp);
		Debugger::debug("Moyenne mobile haute : " . $avg);
		return ($avg);
	}

	public function 	lowMobileAverage() {
		$tmp = array();
		$i = count($this->history) - 1;
		$j = 0;
		while ($i >= 0 && $j < 5) {
			$tmp[] = min($this->history[$i]);
			$i--;
			$j++;
		}
		$avg = array_sum($tmp) / count($tmp);
		Debugger::debug("Moyenne mobile basse : " . $avg);
		return ($avg);
	}


	public function 	trade($cours, $day) {

		$this->actualValue = $cours;

		/* Creation de l'historique */
		if (!isset($this->history[$day / 5])) {
			$this->history[$day / 5][0] = $cours;
		} else {
			$this->history[$day / 5][] = $cours;
		}

		Debugger::debug("Action : " . $cours . " -- Money :: " . $this->money . " -- Nb actions :: " . $this->nbActions);
		$lowMobile  = $this->lowMobileAverage();
		$highMobile = $this->highMobileAverage();
		Debugger::debug("");

		if ($day > 25 && $day != $this->totalDay - 1) {

			if ($this->isUnderLow && $cours > $lowMobile) {

				if ($this->canBuy(1)) {
					$this->buy(1);
				} else {
					echo "wait\n";
				}

			} else if ($this->isHigherHigh && $cours < $highMobile) {

				if ($this->canSell(1)) {
					$this->sell(1);
				} else {
					echo "wait\n";
				}

			} else {
				echo "wait\n";
			}

		} else if ($day == $this->totalDay - 1) {
			Debugger::debug("On est le dernier jour : " . $this->nbActions);
			if ($this->nbActions) {
				if ($this->canSell($this->nbActions)) {
					$this->sell($this->nbActions);
				} else {
					$this->sell($this->nbActions - 1);
				}
			} else {
				echo "wait\n";
			}
		} else {
			echo "wait\n";
		}

		if ($cours < $lowMobile)
			$this->isUnderLow = true;

		if ($cours > $highMobile)
			$this->isHigherHigh = true;

	}


}

?>