<?php

class OtpManager
{
    public static function generateForUser(string $email): string
    {
        $db = Database::instance();

        $otpInDb = $db->get("SELECT `otp`, `generatedOnDateTime` FROM `generatedOtps` WHERE `email` = ?", [$email]);
        $newOtp = str_pad(random_int(1, 999999), 6, "0", STR_PAD_LEFT);
        
        if ($otpInDb != null)
        {
            $query = "UPDATE `generatedOtps` SET `otp` = ?, `isVerified` = 0, `generatedOnDateTime` = CURRENT_TIMESTAMP WHERE `email` = ?";
            $params = [$newOtp, $email];

            $db->execute($query, $params);
        }
        else
        {
            $db->execute(
                "INSERT INTO `generatedOtps` (`email`, `otp`) VALUES (?, ?)",
                [$email, $newOtp]
            );
        }
        
        return $newOtp;
    }

    public static function verifyForUser($email, $otp): bool
    {
        $db = Database::instance();
        
        $record = $db->get("SELECT * FROM `generatedOtps` WHERE `email` = ?", [$email]);
        if ($record == null || $record->otp != $otp)
            return false;

        $submittedTime = (new DateTime())->getTimestamp();
        $generatedTime = (new DateTime($record->generatedOnDateTime))->getTimestamp();
        $difference = $submittedTime - $generatedTime;

        // 5 minutes
        if ($difference > 300)
            return false;
        
        self::setVerified($email);
        return true;
    }

    private static function setVerified($email)
    {
        $db = Database::instance();
        $db->execute("UPDATE `generatedOtps` SET `isVerified` = 1 WHERE `email` = ?", [$email]);
    }

    public static function isVerified($email): bool
    {
        $db = Database::instance();
        $result = $db->get(
            "SELECT `isVerified` FROM `generatedOtps` WHERE `email` = ?",
            [$email]
        );

        return $result != null && $result->isVerified == 1;
    }

    public static function deleteOtpForUser($email)
    {
        $db = Database::instance();
        $db->execute("DELETE FROM `generatedOtps` WHERE `email` = ?", [$email]);
    }
}