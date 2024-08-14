<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use Illuminate\Http\Request;
use libphonenumber\PhoneNumberUtil;
use libphonenumber\NumberParseException;

class GuestController extends Controller
{
    // Получение всех гостей
    public function index()
    {
        $guests = Guest::all();
        return response()->json($guests);
    }

    // Получение одного гостя по ID
    public function show($id)
    {
        $guest = Guest::find($id);

        if (!$guest) {
            return response()->json(['message' => 'Guest not found'], 404);
        }

        return response()->json($guest);
    }

    // Создание нового гостя
    public function store(Request $request)
    {
        $this->validate($request, [
            'first_name' => 'required|string|max:255|regex:/^[\pL\s\-]+$/u',
            'last_name' => 'required|string|max:255|regex:/^[\pL\s\-]+$/u',
            'email' => 'required|string|email|max:255|unique:guests',
            'phone' => ['required', 'string', 'max:20', 'unique:guests', function ($attribute, $value, $fail) {
                if (!$this->isValidPhoneNumber($value)) {
                    $fail('The ' . $attribute . ' is not a valid phone number.');
                }
            }],
            'country' => 'sometimes|string|max:255', // Если страна передана, валидация строки
        ]);

        // Если страна не указана, определяем её по номеру телефона
        $country = $request->input('country');
        if (!$country) {
            $country = $this->getCountryFromPhone($request->input('phone'));
        }

        $guest = Guest::create([
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'country' => $country,
        ]);

        return response()->json($guest, 201);
    }

    // Обновление данных гостя
    public function update(Request $request, $id)
    {
        $guest = Guest::find($id);
    
        if (!$guest) {
            return response()->json(['message' => 'Guest not found'], 404);
        }
    
        $this->validate($request, [
            'first_name' => 'string|max:255|regex:/^[\pL\s\-]+$/u',
            'last_name' => 'string|max:255|regex:/^[\pL\s\-]+$/u',
            'email' => 'string|email|max:255|unique:guests,email,' . $id,
            'phone' => ['string', 'max:20', 'unique:guests,phone,' . $id, function ($attribute, $value, $fail) {
                if (!$this->isValidPhoneNumber($value)) {
                    $fail('The ' . $attribute . ' is not a valid phone number.');
                }
            }],
            'country' => 'sometimes|string|max:255', // Если страна передана, валидация строки
        ]);
    
        // Обновляем значения из запроса
        $guest->fill($request->all());
    
        // Если страна не указана, определяем её по номеру телефона
        if (!$request->has('country')) {
            $guest->country = $this->getCountryFromPhone($request->input('phone'));
        } else {
            $guest->country = $request->input('country');
        }
    
        $guest->save();
    
        return response()->json($guest);
    }
    
    // Удаление гостя
    public function destroy($id)
    {
        $guest = Guest::find($id);

        if (!$guest) {
            return response()->json(['message' => 'Guest not found'], 404);
        }

        $guest->delete();

        return response()->json(['message' => 'Guest deleted']);
    }

    // Вспомогательный метод для определения страны по номеру телефона
    private function getCountryFromPhone($phone)
    {
        $phoneNumberUtil = PhoneNumberUtil::getInstance();
        try {
            $phoneNumber = $phoneNumberUtil->parse($phone, null);
            return $phoneNumberUtil->getRegionCodeForNumber($phoneNumber);
        } catch (NumberParseException $e) {
            // Логируем ошибку и возвращаем null, если страна не определена
            Log::warning("Unable to parse phone number: {$phone}");
            return null; // Или '', если предпочтительнее
        }
    }

    // Получение полного названия страны по региональному коду
    private function getCountryName($regionCode)
    {
        $countries = [
            'RU' => 'Russia',
            'DE' => 'Germany',
            'GB' => 'UK',
            'FR' => 'France',
            // Добавьте другие коды стран по необходимости
        ];

        return $countries[$regionCode] ?? 'Unknown';
    }

    // Вспомогательный метод для валидации номера телефона
    private function isValidPhoneNumber($phone)
    {
        $phoneNumberUtil = PhoneNumberUtil::getInstance();
        try {
            $phoneNumber = $phoneNumberUtil->parse($phone, null);
            return $phoneNumberUtil->isValidNumber($phoneNumber);
        } catch (NumberParseException $e) {
            return false;
        }
    }
}
