@extends('errors.layout')

@section('code', '429')
@section('title', __('Too Many Requests'))
@section('message', __('You have made too many requests in a short time. Wait a moment and try again.'))
@section('retry', true)
