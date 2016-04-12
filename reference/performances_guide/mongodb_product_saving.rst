What about MongoDB storage and bulk product saving performances?
----------------------------------------------------------------

From the 1.4.13 and upper versions, we introduced a new MongoDB product saver, this one is used by default and does not require special configuration.

This new bulk product saver performs **10x faster on average** than the previous implementation and it's used for any bulk product saving, for instance : product import, mass edit and rules execution.
