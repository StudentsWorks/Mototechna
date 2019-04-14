<?php

namespace App\Controllers;

use App\Forms\OrdersForm;
use App\Lib\Auth;
use App\Lib\Controller;

class StoreController extends Controller
{
    public static function rules()
    {
        return [
            'index' => [Auth::requireLoggedIn('/registracia')],
            'cart' => [Auth::requireLoggedIn('/registracia')],
        ];
    }
    public function index()
    {

        $context = $this->app->DB()->getContext();
        $customers = $context->table('sklad');

        return $this->success($this->render('index', [
            'model' => $customers,
        ]));
    }
    public function contact()
    {
        $this->title = "Kontakt";
        return $this->success($this->render('contact'));
    }
    public function cart()
    {

        $this->title = "Košík";
        $context = $this->app->DB()->getContext();
        $cart = $this->app->auth()->session('cart');
        $objednavka = null;
        $form = new OrdersForm(function (OrdersForm $form) use ($context, $cart, &$objednavka) {
            $objednavka = $context->table('objednavka')->insert([
                'zakaznikId' => $this->app->auth()->user->id,
                'prepravaId' => $form->data['preprava'],
            ]);
            $kosik = $context->table('kosik');
            foreach ($cart as $id => $mnozstvo) {

                $kosik->insert([
                    'objednavkaId' => $objednavka->id,
                    'tovarId' => $id,
                    'mnozstvo' => $mnozstvo,
                ]);
            }
            $this->clearCart();
            return true;
        });


        if (isset($this->request->getQueryParams()['add'])) {
            $this->addToCart($this->request->getQueryParams()['add']);
        }
        if (isset($this->request->getQueryParams()['cancel'])) {
            $this->cancelFromCart($this->request->getQueryParams()['cancel']);
        }
        if (isset($this->request->getQueryParams()['remove'])) {
            $this->removeFromCart($this->request->getQueryParams()['remove']);
        }
        $model = $context
            ->table('sklad')
            ->where('id', array_keys($cart));
        $preprava = $context->table('preprava')->fetchPairs('id', 'popis');
        return $this->success($this->render('cart', [
            'model' => $model,
            'cart' => $cart,
            'form' => $form,
            'preprava' => $preprava,
            'objednavka' => $objednavka,
        ]));
    }
    public function validateProductId($id)
    {
        $context = $this->app->DB()->getContext();
        $product = $context->table('sklad')->get($id);
        if (!$product) {
            throw new \Exception('Given product does not exists');
        }
        return $product;
    }

    public function addToCart($id)
    {
        $product = $this->validateProductId($id);
        $cart = $this->app->auth()->session('cart');
        $cart[$product->id] = ($cart[$product->id] ?? 0) + 1;
        if ($cart[$product->id] > $product->zasoby)
            $cart[$product->id] = $product->zasoby;
        $this->app->auth()->session('cart', $cart);
        die($this->redirect('/kosik'));
    }
    public function removeFromCart($id)
    {
        $product = $this->validateProductId($id);
        $cart = $this->app->auth()->session('cart');
        $cart[$product->id] = max(($cart[$product->id] ?? 0) - 1, 0);
        if ($cart[$product->id] === 0) unset($cart[$product->id]);
        $this->app->auth()->session('cart', $cart);
        die($this->redirect('/kosik'));
    }
    public function cancelFromCart($id)
    {
        $product = $this->validateProductId($id);
        $cart = $this->app->auth()->session('cart');
        unset($cart[$product->id]);
        $this->app->auth()->session('cart', $cart);
        die($this->redirect('/kosik'));
    }
    public function clearCart()
    {
        $this->app->auth()->session('cart', []);
    }
}
