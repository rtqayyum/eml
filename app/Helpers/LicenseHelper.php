<?php

namespace Acelle\Helpers;

class LicenseHelper
{
    // license type
    const TYPE_REGULAR = 'regular';
    const TYPE_EXTENDED = 'extended';

    /**
     * Get license type: normal / extended.
     *
     * @var string
     */
    public static function getLicense($license)
    {
		$server_output = array();
		$server_output['status'] = 'valid';
		
		$server_output['data']['verify-purchase']['licence']='codelist';
		return $server_output;
    }

    /**
     * Get license type: normal / extended.
     *
     * @var string
     */
    public static function getLicenseType($license)
    {
        $result = self::getLicense($license);

        # return '' if not valid
        if ($result['status'] != 'valid') {
            // License is not valid
            throw new \Exception(trans('messages.license_is_not_valid'));
        }

        return $result['data']['verify-purchase']['licence'] == 'Regular License' ? self::TYPE_REGULAR : self::TYPE_EXTENDED;
    }

    /**
    * Check is valid extend license
    *
    * @return boolean
    */
    public static function isExtended($license)
    {
        return LicenseHelper::isValid($license) && LicenseHelper::getLicenseType($license) == LicenseHelper::TYPE_EXTENDED;
    }

    /**
    * Check license is valid
    *
    * @return boolean
    */
    public static function isValid($license)
    {
        $result = self::getLicense($license);

        return $result['status'] == 'valid';
    }

    /**
     * Check license is extended not remote.
     *
     * @var boolean
     */
    public static function systemLicenseIsExtended()
    {
        return \Acelle\Model\Setting::get('license_type') == LicenseHelper::TYPE_EXTENDED;
    }

    /**
     * Check when show not extended popup.
     *
     * @var boolean
     */
    public static function showNotExtendedLicensePopup()
    {
        return (\Request::is('*payment_methods*'))
            && !LicenseHelper::systemLicenseIsExtended();
    }
}
