<script lang="ts">
    import axios from "axios";
    import { twMerge } from "tailwind-merge";
    import { createForm } from "felte";
    import { z } from "zod";
    import { validator } from "@felte/validator-zod";
    import { page, router } from "@inertiajs/svelte";

    import BaseModal from "./BaseModal.svelte";
    import { DeleteRT } from "../../../schema";

    export let showState = false;
    export let dataPointed: any = null;
    export let leader: any = null;

    let err = {
        status: false,
        message: null,
    };

    const handleSubmit = async (values) => {};

    const { form, isSubmitting, errors } = createForm<z.infer<typeof DeleteRT>>(
        {
            extend: validator<z.infer<typeof DeleteRT>>({ schema: DeleteRT }),
            onSubmit: async (values) => {
                console.log(values);

                await axios.dele;
                const response = await axios.delete("/api/rt", {
                    headers: {
                        Authorization: `Bearer ${getCookie("token")}`,
                    },
                    data: {
                        id: values.id,
                        _token: $page.props.csrf_token,
                    },
                });
                console.log(response);
                console.log(response.data);

                const { status, message } = response.data;
                if (!status) err = { status: true, message: message };
                else showState = false;
            },
            onError: (values: unknown) => {
                err = { message: values?.response?.data.message, status: true };
                return;
            },
            onSuccess: () => {
                console.log("Success");
            },
        },
    );
</script>

<div
    class={twMerge(
        "fixed top-0 right-0 z-[101] rounded-lg items-center bg-blue-500 text-white text-sm font-bold px-4 py-3",
        err.status ? "flex" : "hidden",
    )}
    role="alert"
>
    <svg
        class="fill-current w-4 h-4 mr-2"
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 20 20"
        ><path
            d="M12.432 0c1.34 0 2.01.912 2.01 1.957 0 1.305-1.164 2.512-2.679 2.512-1.269 0-2.009-.75-1.974-1.99C9.789 1.436 10.67 0 12.432 0zM8.309 20c-1.058 0-1.833-.652-1.093-3.524l1.214-5.092c.211-.814.246-1.141 0-1.141-.317 0-1.689.562-2.502 1.117l-.528-.88c2.572-2.186 5.531-3.467 6.801-3.467 1.057 0 1.233 1.273.705 3.23l-1.391 5.352c-.246.945-.141 1.271.106 1.271.317 0 1.357-.392 2.379-1.207l.6.814C12.098 19.02 9.365 20 8.309 20z"
        /></svg
    >
    <p>{err.message}</p>
</div>

<BaseModal bind:showState>
    <form method="post" use:form>
        <div class="mb-4">
            <label
                for="leader_id"
                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                >Leader</label
            >
            <!-- <input
                type="text"
                disabled
                name="leader_id"
                id="leader_id"
                class="shadow-sm rounded-md w-full px-3 py-2 border border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                value={leader ? leader.civilian_id.fullName : leader}
            /> -->
            <p
                class="shadow-sm rounded-md w-full px-3 py-2 border border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
            >
                {leader ? leader.civilian_id.fullName : leader}
            </p>
        </div>
        <div class="mb-4">
            <label
                for="username"
                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                >RT Number</label
            >
            <input
                type="text"
                readonly
                name="id"
                id="id"
                class="shadow-sm rounded-md w-full px-3 py-2 border border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                required
                bind:value={dataPointed}
            />
            {#if $errors.id}
                <span class="text-sm text-red-500">{$errors.id}</span>
            {/if}
        </div>

        <input
            type="submit"
            value="Delete"
            disabled={$isSubmitting}
            class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-rose-600 hover:bg-rose-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-rose-400"
        />
    </form>
</BaseModal>
