<?php

namespace MPHB\Utils;

if (!defined('ABSPATH')) {
	exit;
}

/**
 * @since 4.10.0
 */
class RoomUtils {

	public static function getRoomTypeId( $roomId ) {
		$roomTypeId = get_post_meta( $roomId, 'mphb_room_type_id', true );
		return absint( $roomTypeId );
	}

}
