<script lang="ts">
    import { createForm } from "felte";
    import { validator } from "@felte/validator-zod";
    import { object, z } from "zod";
    import axios from "axios";
    import { page, router } from "@inertiajs/svelte";

    import { Login } from "@R/schema";
    import { setCookie, getCookie } from "./../Utils/Cokies";

    let title = "Login Page";

    const signIn = async (values: z.infer<typeof Login>) => {
        const response = await axios.post("/api/auth/signin", {
            username: values.username,
            password: values.password,
            _token: $page.props.csrf_token,
        });

        const { status, token, exp } = response.data;
        if (status) setCookie("token", response.data.token, exp);

        router.visit("/civilian", {
            headers: {
                Authorization: `Bearer ${getCookie("token")}`,
            },
        });
    };

    const errHandler = () => {};

    const { form, isSubmitting, errors } = createForm<z.infer<typeof Login>>({
        extend: validator<z.infer<typeof Login>>({ schema: Login }),
        onSubmit: signIn,
        onError: (values: unknown) => {
            console.error(values);
            return;
        },
        onSuccess: () => {
            console.log("Success");
        },
    });
</script>

<svelte:head>
    <title>{title}</title>
</svelte:head>
<!-- component -->
<div
    class="min-h-screen flex items-center justify-center w-full dark:bg-gray-950"
>
    <div
        class="bg-white dark:bg-gray-900 shadow-md rounded-lg px-8 py-6 max-w-md"
    >
        <h1 class="text-2xl font-bold text-center mb-4 dark:text-gray-200">
            Welcome Back!
        </h1>
        <form use:form method="POST">
            <div class="mb-4">
                <label
                    for="username"
                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                    >Username</label
                >
                <input
                    type="text"
                    id="username"
                    name="username"
                    class="shadow-sm rounded-md w-full px-3 py-2 border border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="whois"
                    required
                />
                {#if $errors.username}
                    <span class="text-sm text-red-500">{$errors.username}</span>
                {/if}
            </div>
            <div class="mb-4">
                <label
                    for="password"
                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                    >Password</label
                >
                <input
                    type="password"
                    id="password"
                    name="password"
                    class="shadow-sm rounded-md w-full px-3 py-2 border border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="Enter your password"
                    required
                />
                {#if $errors.password}
                    <span class="text-sm text-red-500">{$errors.password}</span>
                    <br />
                {/if}
                <a
                    href="#"
                    class="text-xs text-gray-600 hover:text-indigo-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >Forgot Password?</a
                >
            </div>
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center">
                    <input
                        type="checkbox"
                        id="remember"
                        class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 focus:outline-none"
                        checked
                    />
                    <label
                        for="remember"
                        class="ml-2 block text-sm text-gray-700 dark:text-gray-300"
                        >Remember me</label
                    >
                </div>
                <a
                    href="#"
                    class="text-xs text-indigo-500 hover:text-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >Create Account</a
                >
            </div>
            <input
                type="submit"
                value="Signin"
                disabled={$isSubmitting}
                class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            />
        </form>
    </div>
</div>
