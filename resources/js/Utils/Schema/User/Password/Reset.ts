import { z } from 'zod'

export const resetSchema = z.object({
    old_password: z.coerce.string().min(1),
    new_password: z.coerce.string().min(8),
    confirm_pass: z.coerce.string().min(8),
}).superRefine(({ confirm_pass, new_password }, ctx) => {
    let format = /[ `!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?~]/;

    if (!format.test(new_password)) {
        ctx.addIssue({
            code: 'custom',
            message: 'Password harus mengandung symbol',
            path: ['new_password']
        })
    }

    if (confirm_pass !== new_password) {
        ctx.addIssue({
            code: "custom",
            message: "Password harus sama",
            path: ["confirm_pass"]
        })
    }
})

export type ResetSchema = z.infer<typeof resetSchema>;


