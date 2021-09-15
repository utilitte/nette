<?php declare(strict_types = 1);

namespace Utilitte\Nette\Utility;

final class ComponentNameBase64
{

	public static function encode(string $subject): string
	{
		return rtrim(str_replace(['/', '+'], ['_s', '_p'], base64_encode($subject)), '=');
	}

	public static function decode(string $subject): string
	{
		return base64_decode(str_replace(['_s', '_p'], ['/', '+'], $subject));
	}

}
