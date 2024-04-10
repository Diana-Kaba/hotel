<?php

namespace MPHB\Core;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class RoomAvailabilityHelper {

	private function __construct() {}


	public static function getActiveRoomsCountForRoomType( int $roomTypeOriginalId ) {

		$roomsAtts = array(
			'post_status'  => 'publish',
		);

		if ( 0 < $roomTypeOriginalId ) {
			$roomsAtts['room_type_id'] = $roomTypeOriginalId;
		}

		return MPHB()->getRoomPersistence()->getCount( $roomsAtts );
	}

	/**
	 * @return array [ room_type_id (int) => [
	 *                    'booked' => [ 'Y-m-d' (string) => rooms_count (int) ]
	 *                    'check-ins' => [ 'Y-m-d' (string) => rooms_count (int) ]
	 *                    'check-outs' => [ 'Y-m-d' (string) => rooms_count (int) ]
	 *                 ], ...
	 *               ]
	 * room_type_id = 0 for booked data of all room types
	 * 'check-ins' and 'check-outs' contain fully booked dates only!
	 */
	public static function getBookedDays() {

		$result = array();

		global $wpdb;

		// example of result
		// booking_id date_name date room_id
		// 2862	mphb_check_in_date	2024-02-20	105
		// 2862	mphb_check_in_date	2024-02-20	99
		// 2862	mphb_check_out_date	2024-02-22	105
		// 2862	mphb_check_out_date	2024-02-22	99
		$bookingsDataRows = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT b.ID AS 'booking_id', b_dates.meta_key AS 'date_name', b_dates.meta_value AS 'date', b_rooms.meta_value AS 'room_id'
				FROM {$wpdb->posts} AS b
				INNER JOIN {$wpdb->postmeta} f ON b.ID = f.post_id AND f.meta_key = 'mphb_check_out_date' AND f.meta_value >= %s
				LEFT JOIN {$wpdb->postmeta} AS b_dates ON b.ID = b_dates.post_id AND b_dates.meta_key IN ('mphb_check_in_date', 'mphb_check_out_date')
				LEFT JOIN {$wpdb->posts} AS b_reserved_rooms ON b.ID = b_reserved_rooms.post_parent
				LEFT JOIN {$wpdb->postmeta} AS b_rooms ON b_reserved_rooms.ID = b_rooms.post_id AND b_rooms.meta_key = '_mphb_room_id'
				WHERE b.post_type = %s AND
				b.post_status IN ('" . implode( "', '", MPHB()->postTypes()->booking()->statuses()->getLockedRoomStatuses() ) . "')",
				current_time( 'Y-m-d' ),
				MPHB()->postTypes()->booking()->getPostType()
			),
			ARRAY_A
		);

		if ( ! empty( $bookingsDataRows ) ) {

			$bookingsData = array();

			foreach ( $bookingsDataRows as $row ) {

				$bookingId = (int) $row[ 'booking_id' ];

				// if ( ! isset( $bookingsData[ $bookingId ] ) ) {

				// 	$bookingsData[ $bookingId ] = array();
				// }

				if ( 'mphb_check_in_date' === $row[ 'date_name' ] ) {

					$bookingsData[ $bookingId ][ 'check_in_date' ] = \DateTime::createFromFormat( 'Y-m-d', $row[ 'date' ] );

				} else {

					$bookingsData[ $bookingId ][ 'check_out_date' ] = \DateTime::createFromFormat( 'Y-m-d', $row[ 'date' ] );
				}

				if ( ! isset( $bookingsData[ $bookingId ][ 'room_ids' ] ) ||
					! in_array( (int) $row[ 'room_id' ], $bookingsData[ $bookingId ][ 'room_ids' ] )
				) {

					$bookingsData[ $bookingId ][ 'room_ids' ][] = (int) $row[ 'room_id' ];
				}
			}

			// get room_ids with corresponding room_type_ids
			$roomIdsWithRoomTypeIds = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT DISTINCT {$wpdb->postmeta}.post_id AS 'room_id', {$wpdb->postmeta}.meta_value AS 'room_type_id'
					FROM {$wpdb->postmeta}
					WHERE {$wpdb->postmeta}.meta_key = 'mphb_room_type_id' AND
						{$wpdb->postmeta}.post_id IN (SELECT {$wpdb->posts}.ID FROM {$wpdb->posts} WHERE {$wpdb->posts}.post_type = %s AND	{$wpdb->posts}.post_status = 'publish') AND
						{$wpdb->postmeta}.meta_value IN (SELECT {$wpdb->posts}.ID FROM {$wpdb->posts} WHERE {$wpdb->posts}.post_type = %s AND	{$wpdb->posts}.post_status = 'publish')",
					MPHB()->postTypes()->room()->getPostType(),
					MPHB()->postTypes()->roomType()->getPostType()
				),
				ARRAY_A
			);

			$roomTypeIdsPerRoomId = wp_list_pluck(
				$roomIdsWithRoomTypeIds,
				'room_type_id',
				'room_id'
			);

			// [ room_type_id => [ Y-m-d => [roomId, roomId, ... ], ... ], ... ]
			$bookedRoomIdsPerDates = array();
			$bookingRules = MPHB()->getCoreAPI()->getBookingRules();

			foreach ( $bookingsData as $bookingData ) {

				$bookedRoomIds              = array();
				$bookedRoomIdsPerRoomTypeId = array();

				foreach ( $bookingData['room_ids'] as $roomId ) {

					// we do not want to take into account deleted rooms or room types
					if ( isset( $roomTypeIdsPerRoomId[ $roomId ] ) ) {

						$bookedRoomIds[] = $roomId;
						$bookedRoomIdsPerRoomTypeId[ (int) $roomTypeIdsPerRoomId[ $roomId ] ][] = $roomId;
					}
				}

				if ( empty( $bookedRoomIds ) ) {
					continue;
				}

				// get booking dates
				$fromDate = $bookingData['check_in_date']->format( 'Y-m-d' );
				$toDate   = $bookingData['check_out_date']->format( 'Y-m-d' );
				$today    = mphb_current_time( 'Y-m-d' );
				$fromDate = $fromDate >= $today ? $fromDate : $today;
				
				$bookingDates = \MPHB\Utils\DateUtils::createDateRangeArray( $fromDate, $toDate );

				// add booking buffer dates
				foreach ( $bookedRoomIdsPerRoomTypeId as $bookedRoomTypeId => $roomIds ) {

					$bookingBufferDays = $bookingRules->getBufferDaysCount(
						$bookedRoomTypeId,
						$bookingData['check_in_date'],
						MPHB()->settings()->main()->isBookingRulesForAdminDisabled()
					);

					$bookingDatesForRoomType = $bookingDates;

					if ( 0 < $bookingBufferDays ) {

						$bufferDatesForRoom = BookingHelper::getBookingBufferDates(
							$bookingData['check_in_date'],
							$bookingData['check_out_date'],
							$bookingBufferDays
						);

						$bookingDatesForRoomType = array_merge( $bookingDatesForRoomType, $bufferDatesForRoom );
					}

					$bookingDatesForRoomType = array_keys( $bookingDatesForRoomType );

					// add booked room ids to booking dates
					foreach ( $bookingDatesForRoomType as $dateYmd ) {

						if ( ! isset( $bookedRoomIdsPerDates[ $bookedRoomTypeId ][ $dateYmd ] ) ) {

							$bookedRoomIdsPerDates[ $bookedRoomTypeId ][ $dateYmd ] = $bookedRoomIds;

						} else {

							$bookedRoomIdsPerDates[ $bookedRoomTypeId ][ $dateYmd ] = array_merge(
								$bookedRoomIdsPerDates[ $bookedRoomTypeId ][ $dateYmd ],
								$bookedRoomIds
							);
						}

						// add data for all room types ( room_type_id = 0 )
						if ( ! isset( $bookedRoomIdsPerDates[ 0 ][ $dateYmd ] ) ) {

							$bookedRoomIdsPerDates[ 0 ][ $dateYmd ] = $bookedRoomIds;

						} else {

							$bookedRoomIdsPerDates[ 0 ][ $dateYmd ] = array_merge(
								$bookedRoomIdsPerDates[ 0 ][ $dateYmd ],
								$bookedRoomIds
							);
						}
					}
				}
			}

			$roomsTotalCountsPerRoomTypeId = array(
				// all rooms from all room types count
				0 => count( $roomIdsWithRoomTypeIds ),
			);

			foreach ( $roomIdsWithRoomTypeIds as $row ) {

				if ( isset( $roomsTotalCountsPerRoomTypeId[ $row['room_type_id'] ] ) ) {
					$roomsTotalCountsPerRoomTypeId[ $row['room_type_id'] ]++;
				} else {
					$roomsTotalCountsPerRoomTypeId[ $row['room_type_id'] ] = 1;
				}
			}

			foreach ( $bookedRoomIdsPerDates as $roomTypeId => $bookedRoomIdsPerDateYmd ) {

				$bookedRoomIdsPerDateYmd    = array_map( 'array_unique', $bookedRoomIdsPerDateYmd );
				$bookedRoomCountsPerDateYmd = array_map( 'count', $bookedRoomIdsPerDateYmd );
				ksort( $bookedRoomCountsPerDateYmd );
		
				$checkInCountsPerDateYmd  = array();
				$checkOutCountsPerDateYmd = array();

				$roomTypeTotalRoomsCount = $roomsTotalCountsPerRoomTypeId[ $roomTypeId ];

				foreach ( $bookedRoomCountsPerDateYmd as $bookedDateYmd => $bookedRoomsCount ) {

					if ( $bookedRoomsCount >= $roomTypeTotalRoomsCount ) {

						$beforeBookedDateYmd = \DateTime::createFromFormat( 'Y-m-d', $bookedDateYmd )->modify( '-1 day' )->format( 'Y-m-d' );
						$afterBookedDateYmd  = \DateTime::createFromFormat( 'Y-m-d', $bookedDateYmd )->modify( '+1 day' )->format( 'Y-m-d' );

						if ( empty( $checkInCountsPerDateYmd ) ||
							! isset( $bookedRoomCountsPerDateYmd[ $beforeBookedDateYmd ] ) ||
							$bookedRoomCountsPerDateYmd[ $beforeBookedDateYmd ] < $bookedRoomsCount
						) {
							$checkInCountsPerDateYmd[ $bookedDateYmd ] = $bookedRoomsCount;
							// we assume that after booked date all guests check-out
							// and clarify it later in cycle
							$checkOutCountsPerDateYmd[ $afterBookedDateYmd ] = $bookedRoomsCount;

						} elseif ( ! empty( $checkOutCountsPerDateYmd ) ) {

							$lastCheckOutDateYmd = array_keys( $checkOutCountsPerDateYmd )[ count( $checkOutCountsPerDateYmd ) - 1 ];
							unset( $checkOutCountsPerDateYmd[ $lastCheckOutDateYmd ] );
							$checkOutCountsPerDateYmd[ $afterBookedDateYmd ] = $bookedRoomsCount;
						}
					}
				}
		
				$result[ $roomTypeId ] = array(
					'booked'     => $bookedRoomCountsPerDateYmd,
					'check-ins'  => $checkInCountsPerDateYmd,
					'check-outs' => $checkOutCountsPerDateYmd,
				);
			}
		}

		return $result;
	}


	public static function getAvailableRoomsCountForRoomType( int $roomTypeOriginalId, \DateTime $date, bool $isIgnoreBookingRules ) {

		$availableRoomsCount = MPHB()->getCoreAPI()->getActiveRoomsCountForRoomType( $roomTypeOriginalId );

		if ( 0 >= $availableRoomsCount ) { // for optimization of calculation
			return $availableRoomsCount;
		}

		$formattedDate = $date->format( 'Y-m-d' );

		$bookedDays = MPHB()->getCoreAPI()->getBookedDaysForRoomType( $roomTypeOriginalId );

		if ( ! empty( $bookedDays['booked'][ $formattedDate ] ) ) {
			$availableRoomsCount = $availableRoomsCount - $bookedDays['booked'][ $formattedDate ];
		}

		if ( 0 >= $availableRoomsCount ) { // for optimization of calculation
			return $availableRoomsCount;
		}

		if ( ! $isIgnoreBookingRules ) {

			$blokedRoomsCount = MPHB()->getCoreAPI()->getBookingRules()->getBlockedRoomsCountsForRoomType(
				$roomTypeOriginalId,
				$date,
				$isIgnoreBookingRules
			);

			$availableRoomsCount = $availableRoomsCount - $blokedRoomsCount;
		}

		return $availableRoomsCount;
	}

	/**
	 * @return string status
	 */
	public static function getRoomTypeAvailabilityStatus( int $roomTypeOriginalId, \DateTime $date, bool $isIgnoreBookingRules ) {

		if ( $date < ( new \DateTime() )->setTime( 0, 0, 0 ) ) {
			return RoomTypeAvailabilityStatus::ROOM_TYPE_AVAILABILITY_STATUS_PAST;
		}

		if ( MPHB()->getCoreAPI()->isBookedDate( $roomTypeOriginalId, $date ) ) {
			return RoomTypeAvailabilityStatus::ROOM_TYPE_AVAILABILITY_STATUS_BOOKED;
		}

		if (  MPHB()->getCoreAPI()->getBookingRules()->isCheckInEarlierThanMinAdvanceDate( $roomTypeOriginalId, $date, $isIgnoreBookingRules )	) {
			return RoomTypeAvailabilityStatus::ROOM_TYPE_AVAILABILITY_STATUS_EARLIER_MIN_ADVANCE;
		}

		if ( MPHB()->getCoreAPI()->getBookingRules()->isCheckInLaterThanMaxAdvanceDate( $roomTypeOriginalId, $date, $isIgnoreBookingRules ) ) {
			return RoomTypeAvailabilityStatus::ROOM_TYPE_AVAILABILITY_STATUS_LATER_MAX_ADVANCE;
		}

		if ( 0 < $roomTypeOriginalId ) {

			$datesRates = MPHB()->getCoreAPI()->getDatesRatesForRoomType( $roomTypeOriginalId );

			if ( ! in_array( $date->format( 'Y-m-d' ), $datesRates ) ) {
				return RoomTypeAvailabilityStatus::ROOM_TYPE_AVAILABILITY_STATUS_NOT_AVAILABLE;
			}

			if ( 0 >= static::getAvailableRoomsCountForRoomType( $roomTypeOriginalId, $date, $isIgnoreBookingRules ) ) {
				return RoomTypeAvailabilityStatus::ROOM_TYPE_AVAILABILITY_STATUS_NOT_AVAILABLE;
			}
		} else {

			$allRoomTypeIds = MPHB()->getCoreAPI()->getAllRoomTypeOriginalIds();

			$formattedDateYmd = $date->format( 'Y-m-d' );

			foreach ( $allRoomTypeIds as $roomTypeId ) {

				$datesRates = MPHB()->getCoreAPI()->getDatesRatesForRoomType( $roomTypeId );

				if ( in_array( $formattedDateYmd, $datesRates ) &&
					0 < static::getAvailableRoomsCountForRoomType( $roomTypeId, $date, $isIgnoreBookingRules )
				) {
					// at least one room type has available room
					return RoomTypeAvailabilityStatus::ROOM_TYPE_AVAILABILITY_STATUS_AVAILABLE;
				}
			}

			return RoomTypeAvailabilityStatus::ROOM_TYPE_AVAILABILITY_STATUS_NOT_AVAILABLE;
		}

		return RoomTypeAvailabilityStatus::ROOM_TYPE_AVAILABILITY_STATUS_AVAILABLE;
	}


	/**
	 * @param $considerCheckIn - if true then check-in date considered as booked if there is no any available room
	 * @param $considerCheckOut - if true then check-out date considered as booked if there is no any available room
	 * @return true if given date is booked (there is no any available room)
	 */
	public static function isBookedDate( int $roomTypeOriginalId, \DateTime $date, $considerCheckIn = true, $considerCheckOut = false ) {

		$bookedDays       = MPHB()->getCoreAPI()->getBookedDaysForRoomType( $roomTypeOriginalId );
		$activeRoomsCount = MPHB()->getCoreAPI()->getActiveRoomsCountForRoomType( $roomTypeOriginalId );

		$formattedDate = $date->format( 'Y-m-d' );

		$isBookedDate = ( ! empty( $bookedDays['booked'][ $formattedDate ] ) &&
			$bookedDays['booked'][ $formattedDate ] >= $activeRoomsCount );

		if ( ! $considerCheckIn && ! empty( $bookedDays['check-ins'][ $formattedDate ] ) ) {
			$isBookedDate = false;
		}

		if ( $considerCheckOut && ! $isBookedDate ) {

			$dateBefore = clone $date;
			$dateBefore->modify( '-1 day' );
			$formattedDateBefore = $dateBefore->format( 'Y-m-d' );

			$isBookedDate = ( ! empty( $bookedDays['booked'][ $formattedDateBefore ] ) &&
				$bookedDays['booked'][ $formattedDateBefore ] >= $activeRoomsCount ) &&
				! empty( $bookedDays['check-outs'][ $formattedDate ] );
		}

		return $isBookedDate;
	}


	/**
	 * @return bool - true if check-in is not allowed in the given date
	 */
	public static function isCheckInNotAllowed( int $roomTypeOriginalId, \DateTime $date, bool $isIgnoreBookingRules ) {

		$availabilityStatus = MPHB()->getCoreAPI()->getRoomTypeAvailabilityStatus( $roomTypeOriginalId, $date, $isIgnoreBookingRules );

		if ( RoomTypeAvailabilityStatus::ROOM_TYPE_AVAILABILITY_STATUS_EARLIER_MIN_ADVANCE === $availabilityStatus ||
			RoomTypeAvailabilityStatus::ROOM_TYPE_AVAILABILITY_STATUS_LATER_MAX_ADVANCE === $availabilityStatus ||
			RoomTypeAvailabilityStatus::ROOM_TYPE_AVAILABILITY_STATUS_PAST === $availabilityStatus ||
			RoomTypeAvailabilityStatus::ROOM_TYPE_AVAILABILITY_STATUS_BOOKED === $availabilityStatus
		) {

			return false;

		} elseif ( RoomTypeAvailabilityStatus::ROOM_TYPE_AVAILABILITY_STATUS_NOT_AVAILABLE === $availabilityStatus ) {

			// check if this is the case when date is blocked by Not Stay In Not Check In and Not Check Out rule
			$isCheckInNotAllowed = MPHB()->getCoreAPI()->getBookingRules()->isCheckInNotAllowed(
				$roomTypeOriginalId,
				$date,
				$isIgnoreBookingRules
			);

			return $isCheckInNotAllowed;
		}

		$isCheckInNotAllowed = MPHB()->getCoreAPI()->getBookingRules()->isCheckInNotAllowed(
			$roomTypeOriginalId,
			$date,
			$isIgnoreBookingRules
		);

		// check Not CheckIn before Not Stay In or Booked days
		if ( ! $isCheckInNotAllowed ) {

			$minStayNights = MPHB()->getCoreAPI()->getBookingRules()->getMinStayLengthReservationDaysCount(
				$roomTypeOriginalId,
				$date,
				$isIgnoreBookingRules
			);

			$checkingDate    = clone $date;
			$nightsAfterDate = 0;

			do {

				$checkingDate->modify( '+1 day' );
				$nightsAfterDate++;

				$checkingDateStatus = MPHB()->getCoreAPI()->getRoomTypeAvailabilityStatus( $roomTypeOriginalId, $checkingDate, $isIgnoreBookingRules );

				$isCheckinDateNotAvailable = RoomTypeAvailabilityStatus::ROOM_TYPE_AVAILABILITY_STATUS_NOT_AVAILABLE === $checkingDateStatus;
				$isCheckingDateBooked      = RoomTypeAvailabilityStatus::ROOM_TYPE_AVAILABILITY_STATUS_BOOKED === $checkingDateStatus;

				$isCheckinDateNotForStayIn = MPHB()->getCoreAPI()->getBookingRules()->isStayInNotAllowed( $roomTypeOriginalId, $checkingDate, $checkingDate, $isIgnoreBookingRules );


				$isBookingNotAllowedInMinStayPeriod = $nightsAfterDate < $minStayNights &&
					( $isCheckinDateNotAvailable || $isCheckinDateNotForStayIn || $isCheckingDateBooked );

				$isCheckOutNotAllowedOnLastDayOfMinStayPeriod = $nightsAfterDate === $minStayNights &&
					MPHB()->getCoreAPI()->getBookingRules()->isCheckOutNotAllowed( $roomTypeOriginalId, $checkingDate, $isIgnoreBookingRules ) &&
					( $isCheckinDateNotAvailable || $isCheckinDateNotForStayIn || $isCheckingDateBooked );

				if ( $isBookingNotAllowedInMinStayPeriod || $isCheckOutNotAllowedOnLastDayOfMinStayPeriod ) {

					$isCheckInNotAllowed = true;
					break;
				}
			} while ( $nightsAfterDate < $minStayNights );
		}

		return $isCheckInNotAllowed;
	}


	/**
	 * @return bool - true if check-out is not allowed in the given date
	 */
	public static function isCheckOutNotAllowed( int $roomTypeOriginalId, \DateTime $date, bool $isIgnoreBookingRules ) {

		$availabilityStatus = MPHB()->getCoreAPI()->getRoomTypeAvailabilityStatus( $roomTypeOriginalId, $date, $isIgnoreBookingRules );

		if ( RoomTypeAvailabilityStatus::ROOM_TYPE_AVAILABILITY_STATUS_PAST === $availabilityStatus ||
			MPHB()->getCoreAPI()->isBookedDate( $roomTypeOriginalId, $date, false, true )
		) {
			return false;
		}

		$isCheckOutNotAllowed = MPHB()->getCoreAPI()->getBookingRules()->isCheckOutNotAllowed( $roomTypeOriginalId, $date, $isIgnoreBookingRules);

		// check Not Check-out after Not Stay-in, Booked or Not Available days
		if ( ! $isCheckOutNotAllowed ) {

			$checkingDate     = clone $date;
			$nightsBeforeDate = 0;

			do {

				$checkingDate->modify( '-1 day' );
				$nightsBeforeDate++;

				$checkingDateStatus = MPHB()->getCoreAPI()->getRoomTypeAvailabilityStatus( $roomTypeOriginalId, $checkingDate, $isIgnoreBookingRules );

				if ( MPHB()->getCoreAPI()->getBookingRules()->isStayInNotAllowed( $roomTypeOriginalId, $checkingDate, $checkingDate, $isIgnoreBookingRules ) ||
					RoomTypeAvailabilityStatus::ROOM_TYPE_AVAILABILITY_STATUS_BOOKED === $checkingDateStatus ||
					RoomTypeAvailabilityStatus::ROOM_TYPE_AVAILABILITY_STATUS_NOT_AVAILABLE === $checkingDateStatus ||
					RoomTypeAvailabilityStatus::ROOM_TYPE_AVAILABILITY_STATUS_PAST === $checkingDateStatus ) {

					$isCheckOutNotAllowed = true;
					break;
				}

				$minStayNights = MPHB()->getCoreAPI()->getBookingRules()->getMinStayLengthReservationDaysCount(
					$roomTypeOriginalId,
					$checkingDate,
					$isIgnoreBookingRules
				);

			} while ( $nightsBeforeDate < $minStayNights );
		}

		return $isCheckOutNotAllowed;
	}


	public static function getRoomTypeAvailabilityData( int $roomTypeOriginalId, \DateTime $date, bool $isIgnoreBookingRules ) {

		$availabilityStatus = MPHB()->getCoreAPI()->getRoomTypeAvailabilityStatus( $roomTypeOriginalId, $date, $isIgnoreBookingRules );

		$result = null;

		if ( RoomTypeAvailabilityStatus::ROOM_TYPE_AVAILABILITY_STATUS_PAST == $availabilityStatus ) {

			$result = new RoomTypeAvailabilityData( $availabilityStatus );

		} else {

			$availableRoomsCount = self::getAvailableRoomsCountForRoomType( $roomTypeOriginalId, $date, $isIgnoreBookingRules );

			$bookedDays     = MPHB()->getCoreAPI()->getBookedDaysForRoomType( $roomTypeOriginalId );
			$formattedDate  = $date->format( 'Y-m-d' );
			$isCheckInDate  = ! empty( $bookedDays['check-ins'][ $formattedDate ] );
			$isСheckOutDate = ! empty( $bookedDays['check-outs'][ $formattedDate ] );

			$isStayInNotAllowed = MPHB()->getCoreAPI()->getBookingRules()->isStayInNotAllowed( $roomTypeOriginalId, $date, $date, $isIgnoreBookingRules );

			$isEarlierThanMinAdvanceDate = MPHB()->getCoreAPI()->getBookingRules()->isCheckInEarlierThanMinAdvanceDate( $roomTypeOriginalId, $date, $isIgnoreBookingRules );

			$isLaterThanMaxAdvanceDate = MPHB()->getCoreAPI()->getBookingRules()->isCheckInLaterThanMaxAdvanceDate( $roomTypeOriginalId, $date, $isIgnoreBookingRules );

			$minStayNights = MPHB()->getCoreAPI()->getBookingRules()->getMinStayLengthReservationDaysCount(
				$roomTypeOriginalId,
				$date,
				$isIgnoreBookingRules
			);

			$maxStayNights = MPHB()->getCoreAPI()->getBookingRules()->getMaxStayLengthReservationDaysCount( $roomTypeOriginalId, $date, $isIgnoreBookingRules );

			$result = new RoomTypeAvailabilityData(
				$availabilityStatus,
				$availableRoomsCount,
				$isCheckInDate,
				$isСheckOutDate,
				$isStayInNotAllowed,
				static::isCheckInNotAllowed( $roomTypeOriginalId, $date, $isIgnoreBookingRules ),
				static::isCheckOutNotAllowed( $roomTypeOriginalId, $date, $isIgnoreBookingRules ),
				$isEarlierThanMinAdvanceDate,
				$isLaterThanMaxAdvanceDate,
				$minStayNights,
				$maxStayNights
			);
		}

		return $result;
	}

	/**
	 * Returns first available date for check-in for room type or
	 * any of room types if $roomTypeOriginalId = 0
	 * @return \DateTime
	 */
	public static function getFirstAvailableCheckInDate( int $roomTypeOriginalId, bool $isIgnoreBookingRules ) {

		$firstAvailableDate = new \DateTime('yesterday');
		$maxCheckDatesCount = 370;

		do {
			$firstAvailableDate->modify( '+1 day' );
			$maxCheckDatesCount--;

			$availabilityStatus = MPHB()->getCoreAPI()->getRoomTypeAvailabilityStatus(
				$roomTypeOriginalId,
				$firstAvailableDate,
				$isIgnoreBookingRules
			);

		} while (
			RoomTypeAvailabilityStatus::ROOM_TYPE_AVAILABILITY_STATUS_AVAILABLE !== $availabilityStatus &&
			RoomTypeAvailabilityStatus::ROOM_TYPE_AVAILABILITY_STATUS_LATER_MAX_ADVANCE !== $availabilityStatus &&
			0 < $maxCheckDatesCount
		);
		
		return $firstAvailableDate;
	}
}
