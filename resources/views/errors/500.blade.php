@extends('errors.layout')

@section('code', '500')
@section('title', __('Server Error'))
@section('message', __('Something went wrong on our side. Try again in a few moments; if the problem persists, contact support.'))
@section('retry', true)
