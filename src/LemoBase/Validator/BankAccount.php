<?php

namespace LemoBase\Validator;

use Zend\Validator\AbstractValidator;

class BankAccount extends AbstractValidator
{
    const INVALID_INPUT = 'invInput';
    const INVALID_ACCOUNT_PREFIX = 'invPrefix';
    const INVALID_ACCOUNT_NUMBER = 'invAccount';
    const INVALID_BANK_CODE = 'invBankCode';

    /**
     * Číselník bankovních kóduů k datu 1.2.2014 (zdroj ČNB)
     *
     * @var array
     */
    protected $bankCodes = array(
        '0100' => 'Komerční banka, a.s.',
        '0300' => 'Československá obchodní banka, a.s.',
        '0600' => 'GE Money Bank, a.s.',
        '0710' => 'Česká národní banka',
        '0800' => 'Česká spořitelna, a.s.',
        '2010' => 'Fio banka, a.s.',
        '2020' => 'Bank of Tokyo-Mitsubishi UFJ (Holland) N.V. Prague Branch, organizační složka',
        '2030' => 'AKCENTA, spořitelní a úvěrní družstvo',
        '2050' => 'WPB Capital, spořitelní družstvo',
        '2060' => 'Citfin, spořitelní družstvo',
        '2070' => 'Moravský Peněžní Ústav – spořitelní družstvo',
        '2100' => 'Hypoteční banka, a.s.',
        '2200' => 'Peněžní dům, spořitelní družstvo',
        '2210' => 'Evropsko-ruská banka, a.s.',
        '2220' => 'Artesa, spořitelní družstvo',
        '2240' => 'Poštová banka, a.s., pobočka Česká republika',
        '2250' => 'Záložna CREDITAS, spořitelní družstvo',
        '2310' => 'ZUNO BANK AG, organizační složka',
        '2600' => 'Citibank Europe plc, organizační složka',
        '2700' => 'UniCredit Bank Czech Republic and Slovakia, a.s.',
        '3020' => 'MEINL BANK Aktiengesellschaft,pobočka Praha',
        '3030' => 'Air Bank a.s.',
        '3500' => 'ING Bank N.V.',
        '4000' => 'LBBW Bank CZ a.s.',
        '4300' => 'Českomoravská záruční a rozvojová banka, a.s.',
        '5400' => 'The Royal Bank of Scotland plc, organizační složka',
        '5500' => 'Raiffeisenbank a.s.',
        '5800' => 'J & T Banka, a.s.',
        '6000' => 'PPF banka a.s.',
        '6100' => 'Equa bank a.s.',
        '6200' => 'COMMERZBANK Aktiengesellschaft, pobočka Praha',
        '6210' => 'mBank S.A., organizační složka',
        '6300' => 'BNP Paribas Fortis SA/NV, pobočka Česká republika',
        '6700' => 'Všeobecná úverová banka a.s., pobočka Praha',
        '6800' => 'Sberbank CZ, a.s.',
        '7910' => 'Deutsche Bank A.G. Filiale Prag',
        '7940' => 'Waldviertler Sparkasse Bank AG',
        '7950' => 'Raiffeisen stavební spořitelna a.s.',
        '7960' => 'Českomoravská stavební spořitelna, a.s.',
        '7970' => 'Wüstenrot-stavební spořitelna a.s.',
        '7980' => 'Wüstenrot hypoteční banka a.s.',
        '7990' => 'Modrá pyramida stavební spořitelna, a.s.',
        '8030' => 'Raiffeisenbank im Stiftland eG pobočka Cheb, odštěpný závod',
        '8040' => 'Oberbank AG pobočka Česká republika',
        '8060' => 'Stavební spořitelna České spořitelny, a.s.',
        '8090' => 'Česká exportní banka, a.s.',
        '8150' => 'HSBC Bank plc - pobočka Praha',
        '8200' => 'PRIVAT BANK AG der Raiffeisenlandesbank Oberösterreich v České republice',
    );

    /**
     * @var array
     */
    protected $messageTemplates = array(
        self::INVALID_INPUT => 'Invalid input',
        self::INVALID_ACCOUNT_PREFIX => 'Ivalid bank account prefix',
        self::INVALID_ACCOUNT_NUMBER => 'Ivalid bank acoount main part',
        self::INVALID_BANK_CODE => 'Invalid bank code',
    );

    /**
     * @param  string $value
     * @return boolean
     */
    public function isValid($value)
    {
        if (!is_string($value)) {
            $this->error(self::INVALID);
            return false;
        }

        $this->setValue($value);
        if(!preg_match('#^\s*((\d{0,6})-)?(\d{1,10})\/(\d{4})\s*$#', $value, $matches)) {
            $this->error(self::INVALID_INPUT);
            return false;
        }

        list( , , $accountPrefix, $accountNo, $bankCode) = $matches;

        // validace předčíslí
        if( ! empty($accountPrefix)) {
            $accountPrefix = str_pad($accountPrefix, 6, '0', STR_PAD_LEFT);
            $sum = 0;
            foreach (array(10, 5, 8, 4, 2, 1) as $index => $weight) {
                $sum += $accountPrefix[$index] * $weight;
            }

            if (0 !== $sum % 11) {
                $this->error(self::INVALID_ACCOUNT_PREFIX);
                return false;
            }
        }

        // validace čísla účtu
        $accountNo = str_pad($accountNo, 10, '0', STR_PAD_LEFT);
        $sum = 0;
        foreach (array(6, 3, 7, 9, 10, 5, 8, 4, 2, 1) as $index => $weight) {
            $sum += $accountNo[$index] * $weight;
        }

        if (0 !== $sum % 11) {
            $this->error(self::INVALID_ACCOUNT_NUMBER);
            return false;
        }

        // validace kódu banky
        if(!array_key_exists($bankCode, $this->bankCodes)) {
            $this->error(self::INVALID_BANK_CODE);
            return false;
        }

        return true;
    }
}
