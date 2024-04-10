<?php

namespace MPHB\Core;

use MPHB\Utils\DateUtils;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Data transfer object for booking rules.
 */
class BookingRulesData {

	/**
	 * @var array [ season_id (int) => \MPHB\Entities\Season, ... ]
	 */
	private $seasons;

	/**
	 * @var array [ season_id (int) => [
	 *                room_type_original_id (int) => [
	 *                   'check_in_days'           => int[] (week days numbers 0 - 6),
	 *                   'check_out_days'          => int[] (week days numbers 0 - 6),
	 *                   'min_advance_reservation' => int,
	 *                   'max_advance_reservation' => int,
	 *                   'min_stay_length'         => int,
	 *                   'max_stay_length'         => int,
	 *                   'buffer_days'             => int,
	 *                ], ...
	 *              ], ...
	 *            ]
	 */
	private $reservationRulesBySeasonIds = array();

	/**
	 * @var array [ room_type_original_id (int) => [
	 *                room_id (int) => [
	 *                   [
	 *                      'date_from'           => string (Ymd),
	 *                      'date_to'             => string (Ymd),
	 *                      'date_period'         => DatePeriod,
	 *                      'not_check_in'        => bool,
	 *                      'not_check_out'       => bool,
	 *                      'not_stay_in'         => bool,
	 *                      'custom_rule_comment' => string
	 *                   ], ...
	 *                ], ...
	 *              ], ...
	 *            ]
	 */
	private $customRulesByRoomTypeIds = array();

	
	private $hasCheckInDaysRules = false;
	private $hasCheckOutDaysRules = false;
	private $hasMinStayLengthRules = false;
	private $hasMaxStayLengthRules = false;
	private $hasMinAdvanceReservationRules = false;
	private $hasMaxAdvanceReservationRules = false;
	private $hasBufferDaysRules = false;
	private $hasNotCheckInRules = false;
	private $hasNotCheckOutRules = false;
	private $hasNotStayInRules = false;

	/**
	 * @var array [ date (string Ymd) => [
	 *                 room_type_original_id (int)  => [
	 *                   'check_in_days'            => int[] (week days numbers 0 - 6),
	 *                   'check_out_days'           => int[] (week days numbers 0 - 6),
	 *                   'min_advance_reservation'  => int,
	 *                   'max_advance_reservation'  => int,
	 *                   'min_stay_length'          => int,
	 *                   'max_stay_length'          => int,
	 *                   'buffer_days'              => int,
	 *                   'not_check_in'             => bool,
	 *                   'not_check_out'            => bool,
	 *                   'not_stay_in'              => bool,
	 *                   'custom_rule_comment'      => string,
	 *                   'custom_rules_for_room_id' => [
	 *                      room_id (int) => [
	 *                         'not_check_in'        => bool,
	 *                         'not_check_out'       => bool,
	 *                         'not_stay_in'         => bool,
	 *                         'custom_rule_comment' => string,
	 *                      ], ...
	 *                   ]
	 *                 ], ...
	 *              ], ...
	 *            ]
	 */
	private $cachedRulesByDates = array();


	public function __construct() {

		/**
		 * [
		 *	'check_in_days'           => [ [ 'season_ids' => int[], 'room_type_ids' => int[], 'check_in_days' => int[] (week day numbers: 0..6) ], ... ],
		 *	'check_out_days'          => [ [ 'season_ids' => int[], 'room_type_ids' => int[], 'check_out_days' => int[] (week day numbers: 0..6) ], ... ],
		 *	'min_stay_length'         => [ [ 'season_ids' => int[], 'room_type_ids' => int[], 'min_stay_length' => int ], ... ],
		 *	'max_stay_length'         => [ [ 'season_ids' => int[], 'room_type_ids' => int[], 'max_stay_length' => int ], ... ],
		 *	'min_advance_reservation' => [ [ 'season_ids' => int[], 'room_type_ids' => int[], 'min_advance_reservation' => int ], ... ],
		 *	'max_advance_reservation' => [ [ 'season_ids' => int[], 'room_type_ids' => int[], 'max_advance_reservation' => int ], ... ]
		 * ]
		 */
		$reservationRules = MPHB()->settings()->bookingRules()->getReservationRules();

		/**
		 * [ [ 'season_ids' => int[], 'room_type_ids' => int[], 'buffer_days' => int ], ... ]
		 */
		$reservationRules['buffer_days'] = MPHB()->settings()->bookingRules()->getBufferRules();
		$this->hasBufferDaysRules        = ! empty( $reservationRules['buffer_days'] );

		$allRoomTypeOriginalIds = MPHB()->getCoreAPI()->getAllRoomTypeOriginalIds();
		$countOfAllRoomTypeOriginalIds = count( $allRoomTypeOriginalIds );

		$seasons       = MPHB()->getSeasonRepository()->findAll();
		$this->seasons = array();

		foreach ( $seasons as $season ) {

			$this->seasons[ $season->getId() ] = $season;
		}


		foreach ( $reservationRules as $ruleType => $ruleDatas ) {

			foreach ( $ruleDatas as $ruleData ) {

				$ruleValue = null;
				$isRuleValueValid = false;

				if ( 'buffer_days' === $ruleType ||
					'min_stay_length' === $ruleType || 'max_stay_length' === $ruleType ||
					'min_advance_reservation' === $ruleType || 'max_advance_reservation' === $ruleType
				) {

					$ruleValue = absint( $ruleData[ $ruleType ] );
					$isRuleValueValid = 0 < $ruleValue;

				} elseif ( 'check_in_days' === $ruleType || 'check_out_days' === $ruleType ) {

					$ruleValue = $ruleData[ $ruleType ];
					$isRuleValueValid = is_array( $ruleValue ) && 0 < count( $ruleValue );
				}

				if ( ! empty( $ruleData['room_type_ids'] ) && is_array( $ruleData['room_type_ids'] ) &&
					! empty( $ruleData['season_ids'] ) && is_array( $ruleData['season_ids'] ) &&
					$isRuleValueValid
				) {

					switch ( $ruleType ) {

						case 'check_in_days':
							$this->hasCheckInDaysRules = true;
							break;
						case 'check_out_days':
							$this->hasCheckOutDaysRules = true;
							break;
						case 'min_stay_length':
							$this->hasMinStayLengthRules = true;
							break;
						case 'max_stay_length':
							$this->hasMaxStayLengthRules = true;
							break;
						case 'min_advance_reservation':
							$this->hasMinAdvanceReservationRules = true;
							break;
						case 'max_advance_reservation':
							$this->hasMaxAdvanceReservationRules = true;
							break;
						case 'buffer_days':
							$this->hasBufferDaysRules = true;
							break;
					}

					$ruleSeasons = $ruleData['season_ids'];

					// if season_ids contains 0 then rule is for all seasons
					if ( in_array( 0, $ruleSeasons ) ) {

						// we keep copy of ruleas for all seasons ( where season_id = 0 ) to be able to find common rules
						$ruleSeasons = array_merge( array( 0 ), array_keys( $this->seasons ) );
					}

					foreach ( $ruleSeasons as $seasonId ) {

						// we expect original room type ids on base language!
						foreach ( $ruleData['room_type_ids'] as $roomTypeOriginalId ) {

							// we do not overwrite rules data and keep first stored one
							if ( ! isset( $this->reservationRulesBySeasonIds[ $seasonId ][ $roomTypeOriginalId ][$ruleType] ) ) {

								$this->reservationRulesBySeasonIds[ $seasonId ][ $roomTypeOriginalId ][$ruleType] = $ruleValue;
							}
						}
					}
				}
			}

			if ( 'buffer_days' !== $ruleType ) {

				foreach ( $this->reservationRulesBySeasonIds as $seasonId => $rulesData ) {

					// set common rule for all room type ids ( $roomTypeId = 0 ) if we have no one 
					// we set it from separate rules for all room type ids in current season
					// if separate rules cover all room type ids in the database
					if ( ! isset( $rulesData[ 0 ][$ruleType] ) ) {

						$countOfAllRoomTypeIdsWithCurrentRuleType = 0;
						$ruleTypeCommonValue = null;

						foreach ( $rulesData as $roomTypeId => $roomTypeRuleData ) {

							if ( 0 !== $roomTypeId && isset( $roomTypeRuleData[ $ruleType ] ) ) {

								$countOfAllRoomTypeIdsWithCurrentRuleType++;

								if ( ( 'min_stay_length' === $ruleType || 'min_advance_reservation' === $ruleType ) &&
									( null === $ruleTypeCommonValue || $ruleTypeCommonValue > $roomTypeRuleData[ $ruleType ] )
								) {

									// searching min rule value as common rule value
									$ruleTypeCommonValue = $roomTypeRuleData[ $ruleType ];

								} elseif ( ( 'max_stay_length' === $ruleType || 'max_advance_reservation' === $ruleType ) &&
									( null === $ruleTypeCommonValue || $ruleTypeCommonValue < $roomTypeRuleData[ $ruleType ] )
								) {

									// searching max rule value as common rule value
									$ruleTypeCommonValue = $roomTypeRuleData[ $ruleType ];

								} elseif ( 'check_in_days' === $ruleType || 'check_out_days' === $ruleType ) {

									if ( null === $ruleTypeCommonValue ) {

										$ruleTypeCommonValue = array();
									}

									$ruleTypeCommonValue = array_merge( $ruleTypeCommonValue, $roomTypeRuleData[ $ruleType ] );
								}
							}
						}

						if ( $countOfAllRoomTypeOriginalIds === $countOfAllRoomTypeIdsWithCurrentRuleType ) {

							if ( 'check_in_days' === $ruleType || 'check_out_days' === $ruleType ) {

								$ruleTypeCommonValue = array_unique( $ruleTypeCommonValue );
							}

							$this->reservationRulesBySeasonIds[ $seasonId ][ 0 ][$ruleType] = $ruleTypeCommonValue;
						}
					}
				}
			}
		}

		/**
		 * [
		 *   'room_type_id' => int,
		 *   'room_id'      => int,
		 *   'date_from'    => string (Y-m-d),
		 *   'date_to'      => string (Y-m-d),
		 *   'restrictions' => [ 'check-in'?, 'check-out'?, 'stay-in'? ],
		 *   'comment'      => string
		 * ]
		 */
		$customRules = MPHB()->settings()->bookingRules()->getCustomRules();

		foreach ( $customRules as $customRule ) {

			$dateFrom = \DateTime::createFromFormat( 'Y-m-d', $customRule['date_from'] );
			$dateTo   = \DateTime::createFromFormat( 'Y-m-d', $customRule['date_to'] );

			if ( false !== $dateFrom && false !== $dateTo ) {

				$roomTypeId = absint( $customRule['room_type_id'] );
				$roomId     = absint( $customRule['room_id'] );

				$isNotCheckIn = in_array( 'check-in', $customRule['restrictions'] );
				$isNotCheckOut = in_array( 'check-out', $customRule['restrictions'] );
				$isNotStayIn = in_array( 'stay-in', $customRule['restrictions'] );

				$this->hasNotCheckInRules = $this->hasNotCheckInRules || $isNotCheckIn;
				$this->hasNotCheckOutRules = $this->hasNotCheckOutRules || $isNotCheckOut;
				$this->hasNotStayInRules = $this->hasNotStayInRules || $isNotStayIn;

				$this->customRulesByRoomTypeIds[ $roomTypeId ][ $roomId ][] = array(
					'date_from'           => $dateFrom->format('Ymd'),
					'date_to'             => $dateTo->format('Ymd'),
					'date_period'         => DateUtils::createDatePeriod( $dateFrom, $dateTo ),
					'not_check_in'        => $isNotCheckIn,
					'not_check_out'       => $isNotCheckOut,
					'not_stay_in'         => $isNotStayIn,
					'custom_rule_comment' => isset( $customRule['comment'] ) ? $customRule['comment'] : '',
				);
			}
		}

		if ( ! $this->hasNotStayInRules ) {
			/**
			 * @since 4.10.0
			 *
			 * @param bool $hasNotStayInRules
			 */
			$this->hasNotStayInRules = apply_filters( 'mphb_has_not_stay_in_rules', $this->hasNotStayInRules );
		}
	}

	/**
	 * @return array [ 'check_in_days'            => int[] (week days numbers 0 - 6),
	 *                 'check_out_days'           => int[] (week days numbers 0 - 6),
	 *                 'min_advance_reservation'  => int,
	 *                 'max_advance_reservation'  => int,
	 *                 'min_stay_length'          => int,
	 *                 'max_stay_length'          => int,
	 *                 'buffer_days'              => int,
	 *                 'not_check_in'             => bool,
	 *                 'not_check_out'            => bool,
	 *                 'not_stay_in'              => bool,
	 *                 'custom_rule_comment'      => string,
	 *                 'custom_rules_for_room_id' => [
	 *                    room_id (int) => [
	 *                       'not_check_in'        => bool,
	 *                       'not_check_out'       => bool,
	 *                       'not_stay_in'         => bool,
	 *                       'custom_rule_comment' => string,
	 *                    ], ...
	 *                 ]
	 *               ]
	 */
	private function getBookingRulesForDate( int $roomTypeOriginalId, \DateTime $requestedDate ) {

		$requestedDateString = $requestedDate->format('Ymd');

		if ( ! isset( $this->cachedRulesByDates[ $requestedDateString ][ $roomTypeOriginalId ] ) ) {

			$result = array();

			// find reservation rules data
			foreach ( $this->seasons as $season ) {

				if ( $season->isDateInSeason( $requestedDate ) ) {

					if ( isset( $this->reservationRulesBySeasonIds[ $season->getId() ][ $roomTypeOriginalId ] ) ) {

						$roomTypeRules = $this->reservationRulesBySeasonIds[ $season->getId() ][ $roomTypeOriginalId ];

						foreach ( $roomTypeRules as $ruleType => $ruleTypeValue ) {

							// we take only first value for each ruleType
							if ( ! isset( $result[ $ruleType ] ) ) {

								$result[ $ruleType ] = $ruleTypeValue;
							}
						}
					}

					// get common rules for room type id = 0 for ruleTypes when we do not have roomType specific rules
					if ( 0 < $roomTypeOriginalId && isset( $this->reservationRulesBySeasonIds[ $season->getId() ][ 0 ] ) ) {

						$roomTypeRules = $this->reservationRulesBySeasonIds[ $season->getId() ][ 0 ];

						foreach ( $roomTypeRules as $ruleType => $ruleTypeValue ) {

							// we take only first value for each ruleType
							if ( ! isset( $result[ $ruleType ] ) ) {

								$result[ $ruleType ] = $ruleTypeValue;
							}
						}
					}
				}
			}

			// find custom rules data for requested date
			$roomTypeSpecificRules = array();

			// we will check common rules first where room type id = 0
			// because custom rules is merging with each other
			if ( isset( $this->customRulesByRoomTypeIds[ 0 ] ) ) {

				$roomTypeSpecificRules[ 0 ] = $this->customRulesByRoomTypeIds[ 0 ];
			}

			if ( 0 < $roomTypeOriginalId && isset( $this->customRulesByRoomTypeIds[ $roomTypeOriginalId ] ) ) {

				$roomTypeSpecificRules[ $roomTypeOriginalId ] = $this->customRulesByRoomTypeIds[ $roomTypeOriginalId ];
			}

			foreach ( $roomTypeSpecificRules as $roomTypeId => $roomTypeRulesData ) {

				foreach ( $roomTypeRulesData as $roomId => $roomRulesData ) {

					foreach ( $roomRulesData as $ruleData ) {

						if ( $requestedDateString >= $ruleData['date_from'] && $requestedDateString <= $ruleData['date_to'] ) {

							if ( 0 < $roomId ) {

								$result['custom_rules_for_room_id'][ $roomId ]['not_check_in'] = ( isset( $result['custom_rules_for_room_id'][ $roomId ]['not_check_in'] ) && 
									$result['custom_rules_for_room_id'][ $roomId ]['not_check_in'] ) || 
									( isset( $ruleData['not_check_in'] ) && $ruleData['not_check_in'] );

								$result['custom_rules_for_room_id'][ $roomId ]['not_check_out'] = (	isset( $result['custom_rules_for_room_id'][ $roomId ]['not_check_out'] ) &&
									$result['custom_rules_for_room_id'][ $roomId ]['not_check_out'] ) || 
									( isset( $ruleData['not_check_out'] ) && $ruleData['not_check_out'] );

								$result['custom_rules_for_room_id'][ $roomId ]['not_stay_in'] = ( isset( $result['custom_rules_for_room_id'][ $roomId ]['not_stay_in'] ) &&
									$result['custom_rules_for_room_id'][ $roomId ]['not_stay_in'] ) ||
									( isset( $ruleData['not_stay_in'] ) && $ruleData['not_stay_in'] );

								if ( ! isset( $result['custom_rules_for_room_id'][ $roomId ]['custom_rule_comment'] ) ) {

									$result['custom_rules_for_room_id'][ $roomId ]['custom_rule_comment'] = $ruleData['custom_rule_comment'];

								} else {

									$result['custom_rules_for_room_id'][ $roomId ]['custom_rule_comment'] .= ', ' . $ruleData['custom_rule_comment'];
								}

							} else {

								$result['not_check_in'] = ( isset( $result['not_check_in'] ) && $result['not_check_in'] ) || 
									( isset( $ruleData['not_check_in'] ) && $ruleData['not_check_in'] );

								$result['not_check_out'] = ( isset( $result['not_check_out'] ) && $result['not_check_out'] ) ||
									( isset( $ruleData['not_check_out'] ) && $ruleData['not_check_out'] );

								$result['not_stay_in'] = ( isset( $result['not_stay_in'] ) && $result['not_stay_in'] ) ||
									( isset( $ruleData['not_stay_in'] ) && $ruleData['not_stay_in'] );

								if ( ! isset( $result['custom_rule_comment'] ) ) {

									$result['custom_rule_comment'] = $ruleData['custom_rule_comment'];

								} else {

									$result['custom_rule_comment'] .= ', ' . $ruleData['custom_rule_comment'];
								}
							}
						}
					}
				}
			}

			$result = array_merge(
				array(
					'check_in_days'            => array( 0, 1, 2, 3, 4, 5, 6 ),
					'in_check_in_days'         => true,
					'check_out_days'           => array( 0, 1, 2, 3, 4, 5, 6 ),
					'in_check_out_days'        => true,
					'min_advance_reservation'  => 0,
					'max_advance_reservation'  => 0,
					'min_stay_length'          => 1,
					'max_stay_length'          => 0,
					'buffer_days'              => 0,
					'not_check_in'             => false,
					'not_check_out'            => false,
					'not_stay_in'              => false,
					'custom_rules_comment'     => '',
					'custom_rules_for_room_id' => array(),
				),
				$result
			);

			$requestedDateWeekDay = (int) $requestedDate->format( 'w' );

			$result['in_check_in_days'] = in_array( $requestedDateWeekDay, $result['check_in_days'] );

			$result['in_check_out_days'] = in_array( $requestedDateWeekDay, $result['check_out_days'] );

			/**
			 * @since 4.10.0
			 *
			 * @param array $bookingRules
			 * @param int $roomTypeId
			 * @param \DateTime $date
			 */
			$result = apply_filters( 'mphb_get_booking_rules_for_date', $result, $roomTypeOriginalId, $requestedDate );

			$this->cachedRulesByDates[ $requestedDateString ][ $roomTypeOriginalId ] = $result;
		}

		return $this->cachedRulesByDates[ $requestedDateString ][ $roomTypeOriginalId ];
	}


	public function isCheckInEarlierThanMinAdvanceDate( int $roomTypeOriginalId, \DateTime $checkInDate, bool $isIgnoreBookingRules ) {

		return ! $isIgnoreBookingRules &&
			$this->hasMinAdvanceReservationRules &&
			DateUtils::calcNightsSinceToday( $checkInDate ) < $this->getMinAdvanceReservationDaysCount(
				$roomTypeOriginalId,
				$checkInDate,
				$isIgnoreBookingRules
			);
	}


	public function getMinAdvanceReservationDaysCount( int $roomTypeOriginalId, \DateTime $requestedDate, bool $isIgnoreBookingRules ): int {

		$result = 0;

		if ( ! $isIgnoreBookingRules && $this->hasMinAdvanceReservationRules ) {

			$bookingRules = $this->getBookingRulesForDate( $roomTypeOriginalId, $requestedDate );
			$result = $bookingRules['min_advance_reservation'];
		}

		return $result;
	}


	public function isCheckInLaterThanMaxAdvanceDate( int $roomTypeOriginalId, \DateTime $checkInDate, bool $isIgnoreBookingRules ) {

		$maxStayDaysCount = $this->getMaxAdvanceReservationDaysCount(
			$roomTypeOriginalId,
			$checkInDate,
			$isIgnoreBookingRules
		);

		return ! $isIgnoreBookingRules &&
			$this->hasMaxAdvanceReservationRules &&
			0 < $maxStayDaysCount &&
			DateUtils::calcNightsSinceToday( $checkInDate ) > $maxStayDaysCount;
	}


	public function getMaxAdvanceReservationDaysCount( int $roomTypeOriginalId, \DateTime $requestedDate, bool $isIgnoreBookingRules ): int {

		$result = 0;

		if ( ! $isIgnoreBookingRules && $this->hasMaxAdvanceReservationRules ) {

			$bookingRules = $this->getBookingRulesForDate( $roomTypeOriginalId, $requestedDate );
			$result = $bookingRules['max_advance_reservation'];
		}

		return $result;
	}


	public function getMinStayDaysCountForAllSeasons( int $roomTypeOriginalId ): int {

		$result = 1;

		if ( isset( $this->reservationRulesBySeasonIds[ 0 ][ $roomTypeOriginalId ]['min_stay_length'] ) ) {

			$result = $this->reservationRulesBySeasonIds[ 0 ][ $roomTypeOriginalId ]['min_stay_length'];

		} elseif ( isset( $this->reservationRulesBySeasonIds[ 0 ][ 0 ]['min_stay_length'] ) ) {

			// we get common rule value if have not specific room type rule
			$result = $this->reservationRulesBySeasonIds[ 0 ][ 0 ]['min_stay_length'];
		}

		return $result;
	}


	public function isMinStayDaysRuleViolated( int $roomTypeOriginalId, \DateTime $checkInDate, \DateTime $checkOutDate, bool $isIgnoreBookingRules ) {

		return ! $isIgnoreBookingRules &&
			$this->hasMinStayLengthRules &&
			DateUtils::calcNights( $checkInDate, $checkOutDate ) < $this->getMinStayLengthReservationDaysCount(
				$roomTypeOriginalId,
				$checkInDate,
				$isIgnoreBookingRules
			);
	}


	public function getMinStayLengthReservationDaysCount( int $roomTypeOriginalId, \DateTime $requestedDate, bool $isIgnoreBookingRules ): int {

		$result = 1;

		if ( ! $isIgnoreBookingRules && $this->hasMinStayLengthRules ) {

			$bookingRules = $this->getBookingRulesForDate( $roomTypeOriginalId, $requestedDate );
			$result = $bookingRules['min_stay_length'];
		}

		return $result;
	}


	public function isMaxStayDaysRuleViolated( int $roomTypeOriginalId, \DateTime $checkInDate, \DateTime $checkOutDate, bool $isIgnoreBookingRules ) {

		$maxStayDaysCount = $this->getMaxStayLengthReservationDaysCount(
			$roomTypeOriginalId,
			$checkInDate,
			$isIgnoreBookingRules
		);

		return ! $isIgnoreBookingRules &&
			$this->hasMaxStayLengthRules &&
			0 < $maxStayDaysCount &&
			DateUtils::calcNights( $checkInDate, $checkOutDate ) > $maxStayDaysCount;
	}


	public function getMaxStayLengthReservationDaysCount( int $roomTypeOriginalId, \DateTime $requestedDate, bool $isIgnoreBookingRules ): int {

		$result = 0;

		if ( ! $isIgnoreBookingRules && $this->hasMaxStayLengthRules ) {

			$bookingRules = $this->getBookingRulesForDate( $roomTypeOriginalId, $requestedDate );
			$result = $bookingRules['max_stay_length'];
		}

		return $result;
	}


	public function hasBufferDaysRules( bool $isIgnoreBookingRules ): bool {

		return ! $isIgnoreBookingRules && $this->hasBufferDaysRules;
	}


	public function getBufferDaysCount( int $roomTypeOriginalId, \DateTime $requestedDate, bool $isIgnoreBookingRules ): int {

		$result = 0;

		if ( ! $isIgnoreBookingRules && $this->hasBufferDaysRules ) {

			$bookingRules = $this->getBookingRulesForDate( $roomTypeOriginalId, $requestedDate );
			$result = $bookingRules['buffer_days'];
		}

		return $result;
	}


	public function getBlockedRoomsCountsForRoomType( int $roomTypeOriginalId, \DateTime $requestedDate, bool $isIgnoreBookingRules ): int {

		$result = 0;

		if ( ! $isIgnoreBookingRules && $this->hasNotStayInRules ) {

			$availableRoomsCount = MPHB()->getCoreAPI()->getActiveRoomsCountForRoomType( $roomTypeOriginalId );

			if ( 0 < $availableRoomsCount ) {

				$bookingRules = $this->getBookingRulesForDate( $roomTypeOriginalId, $requestedDate );

				if ( $bookingRules['not_stay_in'] ) {

					$result = $availableRoomsCount;

				} elseif ( 0 < count( $bookingRules['custom_rules_for_room_id'] ) ) {

					foreach ( $bookingRules['custom_rules_for_room_id'] as $roomSpecificRuleData ) {

						if ( $roomSpecificRuleData['not_stay_in'] ) {
							$result++;
						}
					}
				}
			}
		}

		return $result;
	}


	public function isCheckInNotAllowed( int $roomTypeOriginalId, \DateTime $checkInDate, bool $isIgnoreBookingRules ): bool {

		$result = false;

		if ( ! $isIgnoreBookingRules &&
			( $this->hasNotCheckInRules || $this->hasCheckInDaysRules )
		) {

			$bookingRules = $this->getBookingRulesForDate( $roomTypeOriginalId, $checkInDate );
			$result = $bookingRules['not_check_in'] || ! $bookingRules['in_check_in_days'];
		}

		return $result;
	}


	public function isCheckOutNotAllowed( int $roomTypeOriginalId, \DateTime $checkOutDate, bool $isIgnoreBookingRules ): bool {

		$result = false;

		if ( ! $isIgnoreBookingRules &&
			( $this->hasNotCheckOutRules || $this->hasCheckOutDaysRules )
		) {

			$bookingRules = $this->getBookingRulesForDate( $roomTypeOriginalId, $checkOutDate );
			$result = $bookingRules['not_check_out'] || ! $bookingRules['in_check_out_days'];
		}

		return $result;
	}

	public function isStayInNotAllowed( int $roomTypeOriginalId, \DateTime $checkInDate, \DateTime $checkOutDate, bool $isIgnoreBookingRules ): bool {

		if ( ! $isIgnoreBookingRules && $this->hasNotStayInRules ) {

			$testingDate = clone $checkInDate;
			$checkOutDateString = $checkOutDate->format('Ymd');

			do {

				$bookingRules = $this->getBookingRulesForDate( $roomTypeOriginalId, $testingDate );

				if ( $bookingRules['not_stay_in'] ) {

					return true;
				}

				$testingDate->modify( '+1 day' );
				$testingDateString = $testingDate->format('Ymd');

			} while ( $testingDateString < $checkOutDateString );
		}

		return false;
	}


	public function isBookingRulesViolated( int $roomTypeOriginalId, \DateTime $checkInDate, \DateTime $checkOutDate, bool $isIgnoreBookingRules ): bool {

		return $this->isCheckInEarlierThanMinAdvanceDate( $roomTypeOriginalId, $checkInDate, $isIgnoreBookingRules ) ||
			$this->isCheckInLaterThanMaxAdvanceDate( $roomTypeOriginalId, $checkInDate, $isIgnoreBookingRules ) ||
			$this->isMinStayDaysRuleViolated( $roomTypeOriginalId, $checkInDate, $checkOutDate, $isIgnoreBookingRules ) ||
			$this->isMaxStayDaysRuleViolated( $roomTypeOriginalId, $checkInDate, $checkOutDate, $isIgnoreBookingRules ) ||
			$this->isCheckInNotAllowed( $roomTypeOriginalId, $checkInDate, $isIgnoreBookingRules ) ||
			$this->isCheckOutNotAllowed( $roomTypeOriginalId, $checkOutDate, $isIgnoreBookingRules ) ||
			$this->isStayInNotAllowed( $roomTypeOriginalId, $checkInDate, $checkOutDate, $isIgnoreBookingRules );
	}

	/**
	 * @return int[]
	 */
	public function getUnavailableRoomIds( int $roomTypeOriginalId, \DateTime $checkInDate, \DateTime $checkOutDate, bool $isIgnoreBookingRules ) {

		if ( $isIgnoreBookingRules ) {
			return array();

		} elseif ( ! $this->hasCheckInDaysRules && ! $this->hasCheckOutDaysRules &&
			! $this->hasMinAdvanceReservationRules && ! $this->hasMaxAdvanceReservationRules &&
			! $this->hasMinStayLengthRules && ! $this->hasMaxStayLengthRules &&
			! $this->hasNotCheckInRules && ! $this->hasNotCheckOutRules && ! $this->hasNotStayInRules
		) {
			return array();

		} elseif ( $this->isBookingRulesViolated( $roomTypeOriginalId, $checkInDate, $checkOutDate, $isIgnoreBookingRules ) ) {

			return MPHB()->getRoomPersistence()->findAllIdsByType( $roomTypeOriginalId );
		}

		$unavailableRoomIds = array();

		$testingDate = clone $checkInDate;
		$checkOutDateString = $checkOutDate->format('Ymd');

		do {

			$bookingRules = $this->getBookingRulesForDate( $roomTypeOriginalId, $testingDate );

			foreach ( $bookingRules['custom_rules_for_room_id'] as $roomId => $roomSpecificRuleData ) {

				if ( $roomSpecificRuleData['not_stay_in'] ) {

					$unavailableRoomIds[] = $roomId;
				}
			}

			$testingDate->modify( '+1 day' );
			$testingDateString = $testingDate->format('Ymd');

		} while ( $testingDateString < $checkOutDateString );

		$unavailableRoomIds = array_unique( $unavailableRoomIds );
		// reset keys after array_unique() and sort room ids
		sort( $unavailableRoomIds );

		return $unavailableRoomIds;
	}

	/**
	 * @since 4.10.0 added new parameter - $period.
	 *
	 * @param DatePeriod|array $period Optional. Null by default (not limited by period).
	 * @return array [ room_id (int) => [ date (string as Y-m-d) => 'comment_1, comment_2, ...' ], ... ]
	 */
	public function getNotStayInComments( int $roomTypeOriginalId, array $roomIds, $period = null ) {

		$result = array();

		$roomTypeSpecificRules = array();

		if ( isset( $this->customRulesByRoomTypeIds[ 0 ] ) ) {

			$roomTypeSpecificRules[ 0 ] = $this->customRulesByRoomTypeIds[ 0 ];
		}

		if ( 0 < $roomTypeOriginalId && isset( $this->customRulesByRoomTypeIds[ $roomTypeOriginalId ] ) ) {

			$roomTypeSpecificRules[ $roomTypeOriginalId ] = $this->customRulesByRoomTypeIds[ $roomTypeOriginalId ];
		}

		foreach ( $roomTypeSpecificRules as $roomTypeId => $roomTypeRulesData ) {

			foreach ( $roomTypeRulesData as $roomId => $roomRulesData ) {

				foreach ( $roomRulesData as $ruleData ) {

					if ( $ruleData['not_stay_in'] ) {

						$ruleDate = \DateTime::createFromFormat( 'Ymd', $ruleData['date_from'] );
						$ruleDateString = $ruleDate->format('Ymd');
						$formattedRuleDate = $ruleDate->format('Y-m-d');

						if ( ! is_null( $period )
							&& ! DateUtils::isPeriodsIntersect( $ruleData['date_period'], $period )
						) {
							continue; // Skip rule
						}

						do {

							if ( 0 === $roomId ) {

								foreach ( $roomIds as $id ) {

									if ( empty( $result[ $id ][ $formattedRuleDate ] ) ) {
										$result[ $id ][ $formattedRuleDate ] = $ruleData['custom_rule_comment'];
									} else {
										$result[ $id ][ $formattedRuleDate ] .= ', ' . $ruleData['custom_rule_comment'];
									}
								}
							} else {

								if ( empty( $result[ $roomId ][ $formattedRuleDate ] ) ) {
									$result[ $roomId ][ $formattedRuleDate ] = $ruleData['custom_rule_comment'];
								} else {
									$result[ $roomId ][ $formattedRuleDate ] .= ', ' . $ruleData['custom_rule_comment'];
								}
							}

							$ruleDate->modify( '+1 day' );
							$ruleDateString = $ruleDate->format('Ymd');
							$formattedRuleDate = $ruleDate->format('Y-m-d');

						} while ( $ruleDateString <= $ruleData['date_to'] );
					}
				}
			}
		}

		/**
		 * @since 4.10.0
		 *
		 * @param array $calendarComments
		 * @param int $roomTypeId
		 * @param int[] $roomIds
		 * @param ?array $period
		 */
		$result = apply_filters( 'mphb_get_calendar_comments_for_room_type', $result, $roomTypeOriginalId, $roomIds, $period );

		return $result;
	}

	/**
	 * @return array [ room_id (int) => [ date (string as Y-m-d) => 'comment_1, comment_2, ...' ], ... ]
	 */
	public function getNotStayInRulesData( int $roomTypeOriginalId, int $requestedRoomId ) {

		$result = array();

		$roomTypeSpecificRules = array();

		if ( isset( $this->customRulesByRoomTypeIds[ 0 ] ) ) {

			$roomTypeSpecificRules[ 0 ] = $this->customRulesByRoomTypeIds[ 0 ];
		}

		if ( 0 < $roomTypeOriginalId && isset( $this->customRulesByRoomTypeIds[ $roomTypeOriginalId ] ) ) {

			$roomTypeSpecificRules[ $roomTypeOriginalId ] = $this->customRulesByRoomTypeIds[ $roomTypeOriginalId ];
		}

		foreach ( $roomTypeSpecificRules as $roomTypeId => $roomTypeRulesData ) {

			foreach ( $roomTypeRulesData as $roomId => $roomRulesData ) {

				foreach ( $roomRulesData as $ruleData ) {

					if ( $ruleData['not_stay_in'] &&
						( 0 === $requestedRoomId || 0 === $roomId || $requestedRoomId === $roomId )
					) {

						$result[] = array(
							'roomTypeId' => $roomTypeOriginalId,
							'roomId'     => $roomId,
							'startDate'  => \DateTime::createFromFormat( 'Ymd', $ruleData['date_from'] ),
							'endDate'    => \DateTime::createFromFormat( 'Ymd', $ruleData['date_to'] ),
							'comment'    => $ruleData['custom_rule_comment'],
						);
					}
				}
			}
		}

		/**
		 * @since 4.10.0
		 *
		 * @param array $adminBlocks
		 * @param int $roomTypeId
		 * @param int $roomId
		 */
		$result = apply_filters( 'mphb_get_admin_blocks_for_export', $result, $roomTypeOriginalId, $requestedRoomId );

		return $result;
	}
}
